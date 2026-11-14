<?php

namespace Database\Seeders;

use App\Models\Endereco;
use App\Models\SolicitacaoCompra\SolicitacaoCompra;
use App\Models\Tarefa\Quadro;
use App\Models\Tarefa\StatusQuadro;
use App\Models\Tarefa\Tarefa;
use App\Models\Unidade;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Popula o banco com um volume alto de dados em todas as tabelas,
 * pensado para testar paginação e navegação nas telas.
 *
 * Não apaga nada: apenas adiciona registros aos já existentes.
 */
class DadosVolumososSeeder extends Seeder
{
    private const TOTAL_USUARIOS = 150;

    private const TOTAL_QUADROS = 45;

    private const TOTAL_TAREFAS = 1200;

    private const TOTAL_SOLICITACOES = 400;

    private const TOTAL_UNIDADES = 30;

    private const TOTAL_ENDERECOS_USUARIOS = 60;

    private const PAPEIS = ['Quadros e Tarefas', 'Compras', 'Aprovador Compras', 'Usuários'];

    private const STATUS_FLUXO = [
        'A Fazer',
        'Em Andamento',
        'Em Revisão',
        'Concluído',
    ];

    private const STATUS_SOLICITACAO = [
        SolicitacaoCompra::STATUS_PENDENTE,
        SolicitacaoCompra::STATUS_ENVIADO_DIRETORIA,
        SolicitacaoCompra::STATUS_APROVADO_DIRETORIA,
        SolicitacaoCompra::STATUS_REJEITADO_DIRETORIA,
        SolicitacaoCompra::STATUS_ANALISE_PROJETOS,
        SolicitacaoCompra::STATUS_RECURSOS_DISPONIVEIS_COMPRAS,
        SolicitacaoCompra::STATUS_RECURSOS_FUNDACAO,
        SolicitacaoCompra::STATUS_CONCLUIDO,
        SolicitacaoCompra::STATUS_CANCELADO,
    ];

    public function run(): void
    {
        $usuarios = $this->criarUsuarios();
        $quadros = $this->criarQuadros($usuarios);
        $this->criarTarefas($quadros, $usuarios);
        $this->criarSolicitacoes($usuarios);
        $this->criarUnidadesEEnderecos($usuarios);
    }

    /**
     * @return \Illuminate\Support\Collection<int, Usuario>
     */
    private function criarUsuarios()
    {
        $criados = Usuario::factory()
            ->count(self::TOTAL_USUARIOS)
            ->create()
            ->each(function (Usuario $usuario, int $indice) {
                $usuario->assignRole(self::PAPEIS[$indice % count(self::PAPEIS)]);
            });

        // Todos os usuários (inclusive os que já existiam) para sortear relações.
        return Usuario::query()->get();
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Usuario>  $usuarios
     * @return \Illuminate\Support\Collection<int, Quadro>
     */
    private function criarQuadros($usuarios)
    {
        $nomes = ['Marketing', 'Financeiro', 'Operações', 'Suporte', 'Projetos Especiais', 'Produto', 'Comercial', 'Administrativo'];
        $quadros = collect();

        foreach (range(1, self::TOTAL_QUADROS) as $i) {
            $quadro = Quadro::create([
                'nome' => fake()->randomElement($nomes)." {$i}",
                'descricao' => fake()->boolean(80) ? fake()->sentence(10) : null,
                'usuario_id' => $usuarios->random()->id,
            ]);

            foreach (self::STATUS_FLUXO as $nomeStatus) {
                StatusQuadro::create([
                    'nome' => $nomeStatus,
                    'descricao' => null,
                    'quadro_id' => $quadro->id,
                ]);
            }

            // Compartilha o quadro com 1 a 4 usuários aleatórios.
            $quadro->usuarios()->syncWithoutDetaching(
                $usuarios->random(random_int(1, 4))->pluck('id')->all()
            );

            $quadros->push($quadro);
        }

        return $quadros;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Quadro>  $quadros
     * @param  \Illuminate\Support\Collection<int, Usuario>  $usuarios
     */
    private function criarTarefas($quadros, $usuarios): void
    {
        $statusPorQuadro = StatusQuadro::query()
            ->whereIn('quadro_id', $quadros->pluck('id'))
            ->get()
            ->groupBy('quadro_id');

        foreach (range(1, self::TOTAL_TAREFAS) as $__) {
            $quadro = $quadros->random();
            $status = $statusPorQuadro[$quadro->id]->random();

            $tarefa = Tarefa::create([
                'nome' => fake()->sentence(random_int(3, 6)),
                'descricao' => fake()->boolean(60) ? '<p>'.fake()->paragraph().'</p>' : null,
                'status_id' => $status->id,
                'quadro_id' => $quadro->id,
                'data_inicio' => fake()->boolean(60) ? fake()->dateTimeBetween('-3 months', 'now') : null,
                'data_fim' => fake()->boolean(35) ? fake()->dateTimeBetween('now', '+2 months') : null,
            ]);

            // Compartilha a tarefa com 0 a 2 usuários aleatórios.
            if (fake()->boolean(60)) {
                $tarefa->usuarios()->syncWithoutDetaching(
                    $usuarios->random(random_int(1, 2))->pluck('id')->all()
                );
            }
        }
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Usuario>  $usuarios
     */
    private function criarSolicitacoes($usuarios): void
    {
        $titulos = [
            'Reembolso de viagem',
            'Compra de material de escritório',
            'Contratação de fornecedor',
            'Assinatura de software',
            'Manutenção de equipamento',
            'Hospedagem de evento',
            'Compra de móveis',
            'Renovação de licença',
            'Serviço de consultoria',
            'Compra de hardware',
        ];

        foreach (range(1, self::TOTAL_SOLICITACOES) as $i) {
            $status = self::STATUS_SOLICITACAO[$i % count(self::STATUS_SOLICITACAO)];
            $solicitante = $usuarios->random();
            $analista = $usuarios->where('id', '!=', $solicitante->id)->random();

            $emFluxoCompras = in_array($status, [
                SolicitacaoCompra::STATUS_RECURSOS_DISPONIVEIS_COMPRAS,
                SolicitacaoCompra::STATUS_RECURSOS_FUNDACAO,
                SolicitacaoCompra::STATUS_CONCLUIDO,
            ], true);

            $passouDiretoria = in_array($status, [
                SolicitacaoCompra::STATUS_APROVADO_DIRETORIA,
                SolicitacaoCompra::STATUS_REJEITADO_DIRETORIA,
                SolicitacaoCompra::STATUS_ANALISE_PROJETOS,
                SolicitacaoCompra::STATUS_RECURSOS_DISPONIVEIS_COMPRAS,
                SolicitacaoCompra::STATUS_RECURSOS_FUNDACAO,
                SolicitacaoCompra::STATUS_CONCLUIDO,
            ], true);

            SolicitacaoCompra::create([
                'titulo' => fake()->randomElement($titulos),
                'valor' => fake()->numberBetween(500, 2_000_000),
                'prazo' => fake()->boolean(70) ? fake()->dateTimeBetween('now', '+6 months')->format('Y-m-d') : null,
                'descricao' => fake()->boolean(70) ? '<p>'.fake()->paragraph().'</p>' : null,
                'usuario_id' => $solicitante->id,
                'status' => $status,
                'prioridade' => fake()->randomElement(['baixa', 'media', 'alta', 'urgente']),
                'motivo_rejeicao' => $status === SolicitacaoCompra::STATUS_REJEITADO_DIRETORIA
                    ? 'Orçamento incompatível com as diretrizes vigentes ou documentação incompleta.'
                    : null,
                'recursos_existem' => $emFluxoCompras ? fake()->boolean(70) : null,
                'status_compra' => $emFluxoCompras
                    ? fake()->randomElement(['Aguardando cotação', 'Comprado', 'Em trânsito', 'Entregue'])
                    : null,
                'diretoria_por' => $passouDiretoria ? $analista->id : null,
                'projetos_por' => $emFluxoCompras ? $analista->id : null,
                'compras_por' => in_array($status, [
                    SolicitacaoCompra::STATUS_RECURSOS_DISPONIVEIS_COMPRAS,
                    SolicitacaoCompra::STATUS_CONCLUIDO,
                ], true) ? $analista->id : null,
                'concluido_por' => $status === SolicitacaoCompra::STATUS_CONCLUIDO ? $analista->id : null,
                'aprovado_cancelado_por' => $status === SolicitacaoCompra::STATUS_CANCELADO ? $analista->id : null,
            ]);
        }
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Usuario>  $usuarios
     */
    private function criarUnidadesEEnderecos($usuarios): void
    {
        $unidades = collect();

        foreach (range(1, self::TOTAL_UNIDADES) as $i) {
            $dono = $usuarios->random();

            $unidade = new Unidade([
                'nome' => 'Unidade '.fake()->city()." {$i}",
                'cnpj' => $this->cnpjFalso(),
            ]);
            $unidade->usuario_id = $dono->id;
            // A migration exige as colunas polimórficas em `unidades`; como não há uso definido,
            // apontamos para a própria unidade após o insert.
            $unidade->enderecoable_type = Unidade::class;
            $unidade->enderecoable_id = 0;
            $unidade->save();
            $unidade->enderecoable_id = $unidade->id;
            $unidade->save();

            $this->criarEndereco(Unidade::class, $unidade->id, $dono->id);
            $unidades->push($unidade);
        }

        // Endereços para uma amostra de usuários.
        foreach ($usuarios->random(min(self::TOTAL_ENDERECOS_USUARIOS, $usuarios->count())) as $usuario) {
            $this->criarEndereco(Usuario::class, $usuario->id, $usuario->id);
        }
    }

    private function criarEndereco(string $tipo, int $id, int $usuarioId): void
    {
        $endereco = new Endereco([
            'logradouro' => fake()->streetName(),
            'numero' => (string) random_int(1, 5000),
            'complemento' => fake()->boolean(30) ? 'Sala '.random_int(1, 200) : null,
            'bairro' => fake()->word(),
            'cidade' => fake()->city(),
            'estado' => fake()->stateAbbr(),
            'cep' => fake()->postcode(),
        ]);
        $endereco->enderecoable_type = $tipo;
        $endereco->enderecoable_id = $id;
        $endereco->usuario_id = $usuarioId;
        $endereco->save();
    }

    private function cnpjFalso(): string
    {
        $n = fn () => random_int(0, 9);

        return sprintf(
            '%d%d.%d%d%d.%d%d%d/%d%d%d%d-%d%d',
            $n(), $n(), $n(), $n(), $n(), $n(), $n(), $n(), $n(), $n(), $n(), $n(), $n(), $n()
        );
    }
}
