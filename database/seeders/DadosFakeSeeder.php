<?php

namespace Database\Seeders;

use App\Models\SolicitacaoPagamento\SolicitacaoPagamento;
use App\Models\Tarefa\Quadro;
use App\Models\Tarefa\StatusQuadro;
use App\Models\Tarefa\Tarefa;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

class DadosFakeSeeder extends Seeder
{
    public function run(): void
    {
        $papeis = ['Quadros e Tarefas', 'Pagamentos', 'Aprovador Pagamentos', 'Usuários'];

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
            SolicitacaoPagamento::STATUS_PENDENTE,
            SolicitacaoPagamento::STATUS_PENDENTE,
            SolicitacaoPagamento::STATUS_APROVADO,
            SolicitacaoPagamento::STATUS_CONCLUIDO,
            SolicitacaoPagamento::STATUS_CANCELADO,
        ];

        foreach (range(1, 20) as $__) {
            $status = fake()->randomElement($statusDistribuicao);
            $solicitante = $usuarios->random();
            $analista = $usuarios->except([$solicitante->id])->random();

            SolicitacaoPagamento::create([
                'titulo' => fake()->randomElement([
                    'Reembolso de viagem', 'Compra de material de escritório', 'Pagamento de fornecedor',
                    'Assinatura de software', 'Manutenção de equipamento', 'Hospedagem de evento',
                ]),
                'valor' => fake()->numberBetween(1000, 500000),
                'descricao' => fake()->boolean(70) ? '<p>'.fake()->paragraph().'</p>' : null,
                'usuario_id' => $solicitante->id,
                'status' => $status,
                'aprovado_cancelado_por' => in_array($status, [
                    SolicitacaoPagamento::STATUS_APROVADO,
                    SolicitacaoPagamento::STATUS_CONCLUIDO,
                    SolicitacaoPagamento::STATUS_CANCELADO,
                ]) ? $analista->id : null,
                'concluido_por' => $status === SolicitacaoPagamento::STATUS_CONCLUIDO ? $analista->id : null,
            ]);
        }
    }
}
