<?php

namespace Database\Seeders;

use App\Models\SolicitacaoCompra\SolicitacaoCompra;
use App\Models\Tarefa\Quadro;
use App\Models\Tarefa\StatusQuadro;
use App\Models\Tarefa\Tarefa;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

class DadosFakeSeeder extends Seeder
{
    public function run(): void
    {
        $papeis = ['Quadros e Tarefas', 'Compras', 'Aprovador Compras', 'Usuários'];

        $usuarios = Usuario::factory()
            ->count(8)
            ->create()
            ->each(function (Usuario $usuario, int $indice) use ($papeis) {
                $usuario->assignRole($papeis[$indice % count($papeis)]);
            });

        $usuarios->prepend(Usuario::query()->where('email', 'test@example.com')->firstOrFail());

        foreach (range(1, 4) as $i) {
            $quadro = Quadro::create([
                'nome' => fake()->randomElement(['Marketing', 'Financeiro', 'Operações', 'Suporte', 'Projetos Especiais'])." $i",
                'descricao' => fake()->sentence(),
                'usuario_id' => $usuarios->random()->id,
            ]);

            $statusList = collect(['A Fazer', 'Em Andamento', 'Em Revisão', 'Concluído'])
                ->map(fn (string $nome) => StatusQuadro::create([
                    'nome' => $nome,
                    'descricao' => null,
                    'quadro_id' => $quadro->id,
                ]));

            foreach (range(1, random_int(6, 14)) as $__) {
                Tarefa::create([
                    'nome' => fake()->sentence(4),
                    'descricao' => fake()->boolean(60) ? '<p>'.fake()->paragraph().'</p>' : null,
                    'status_id' => $statusList->random()->id,
                    'quadro_id' => $quadro->id,
                    'data_inicio' => fake()->boolean(50) ? fake()->dateTimeBetween('-1 month', 'now') : null,
                    'data_fim' => fake()->boolean(30) ? fake()->dateTimeBetween('now', '+1 month') : null,
                ]);
            }
        }

        $statusDistribuicao = [
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

        foreach (range(1, 25) as $__) {
            $status = fake()->randomElement($statusDistribuicao);
            $solicitante = $usuarios->random();
            $analista = $usuarios->except([$solicitante->id])->random();
            $recursosExistem = in_array($status, [
                SolicitacaoCompra::STATUS_RECURSOS_DISPONIVEIS_COMPRAS,
                SolicitacaoCompra::STATUS_RECURSOS_FUNDACAO,
                SolicitacaoCompra::STATUS_CONCLUIDO,
            ]) ? fake()->boolean(70) : null;

            SolicitacaoCompra::create([
                'titulo' => fake()->randomElement([
                    'Reembolso de viagem', 'Compra de material de escritório', 'Contratação de fornecedor',
                    'Assinatura de software', 'Manutenção de equipamento', 'Hospedagem de evento',
                ]),
                'valor' => fake()->numberBetween(1000, 500000),
                'descricao' => fake()->boolean(70) ? '<p>'.fake()->paragraph().'</p>' : null,
                'usuario_id' => $solicitante->id,
                'status' => $status,
                'prioridade' => fake()->randomElement(['baixa', 'media', 'alta', 'urgente']),
                'motivo_rejeicao' => $status === SolicitacaoCompra::STATUS_REJEITADO_DIRETORIA
                    ? 'Orçamento incompatível com as diretrizes vigentes ou documentação incompleta.'
                    : null,
                'recursos_existem' => $recursosExistem,
                'status_compra' => in_array($status, [
                    SolicitacaoCompra::STATUS_RECURSOS_DISPONIVEIS_COMPRAS,
                    SolicitacaoCompra::STATUS_RECURSOS_FUNDACAO,
                    SolicitacaoCompra::STATUS_CONCLUIDO,
                ]) ? fake()->randomElement(['Aguardando cotação', 'Comprado', 'Em trânsito', 'Entregue']) : null,
                'diretoria_por' => in_array($status, [
                    SolicitacaoCompra::STATUS_APROVADO_DIRETORIA,
                    SolicitacaoCompra::STATUS_REJEITADO_DIRETORIA,
                    SolicitacaoCompra::STATUS_ANALISE_PROJETOS,
                    SolicitacaoCompra::STATUS_RECURSOS_DISPONIVEIS_COMPRAS,
                    SolicitacaoCompra::STATUS_RECURSOS_FUNDACAO,
                    SolicitacaoCompra::STATUS_CONCLUIDO,
                ]) ? $analista->id : null,
                'projetos_por' => in_array($status, [
                    SolicitacaoCompra::STATUS_RECURSOS_DISPONIVEIS_COMPRAS,
                    SolicitacaoCompra::STATUS_RECURSOS_FUNDACAO,
                    SolicitacaoCompra::STATUS_CONCLUIDO,
                ]) ? $analista->id : null,
                'compras_por' => in_array($status, [
                    SolicitacaoCompra::STATUS_RECURSOS_DISPONIVEIS_COMPRAS,
                    SolicitacaoCompra::STATUS_CONCLUIDO,
                ]) ? $analista->id : null,
                'concluido_por' => $status === SolicitacaoCompra::STATUS_CONCLUIDO ? $analista->id : null,
            ]);
        }
    }
}
