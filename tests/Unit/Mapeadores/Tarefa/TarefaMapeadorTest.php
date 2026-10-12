<?php

namespace Tests\Unit\Mapeadores\Tarefa;

use App\Mapeadores\Tarefa\TarefaMapeador;
use App\Models\Tarefa\Tarefa as TarefaModelo;
use Core\Domain\Tarefa\Entities\Tarefa as TarefaEntidade;
use DateTimeImmutable;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TarefaMapeadorTest extends TestCase
{
    private TarefaMapeador $mapeador;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mapeador = new TarefaMapeador;
    }

    public function test_para_entidade_mapeia_todos_os_atributos(): void
    {
        $modelo = new TarefaModelo;
        $modelo->id = 10;
        $modelo->nome = 'Minha Tarefa';
        $modelo->status_id = 1;
        $modelo->quadro_id = 2;
        $modelo->descricao = 'Desc da tarefa';
        $modelo->data_inicio = Carbon::parse('2026-03-01 08:00:00');
        $modelo->data_fim = Carbon::parse('2026-03-05 18:00:00');
        $modelo->criado_em = Carbon::parse('2026-03-01 07:00:00');
        $modelo->atualizado_em = Carbon::parse('2026-03-01 07:30:00');
        $modelo->apagado_em = null;

        $entidade = $this->mapeador->paraEntidade($modelo);

        $this->assertSame(10, $entidade->id());
        $this->assertSame('Minha Tarefa', $entidade->nome());
        $this->assertSame(1, $entidade->statusId());
        $this->assertSame(2, $entidade->quadroId());
        $this->assertSame('Desc da tarefa', $entidade->descricao());
        $this->assertEquals(new DateTimeImmutable('2026-03-01 08:00:00'), $entidade->dataInicio());
        $this->assertEquals(new DateTimeImmutable('2026-03-05 18:00:00'), $entidade->dataFim());
    }

    public function test_para_modelo_mapeia_entidade_para_modelo(): void
    {
        $entidade = new TarefaEntidade(
            id: 25,
            nome: 'Tarefa Nova',
            status_id: 3,
            quadro_id: 4,
            descricao: 'Desc',
            data_inicio: new DateTimeImmutable('2026-04-01 10:00:00'),
            data_fim: new DateTimeImmutable('2026-04-02 18:00:00'),
        );

        $modelo = $this->mapeador->paraModelo($entidade);

        $this->assertSame(25, $modelo->getKey());
        $this->assertSame('Tarefa Nova', $modelo->nome);
        $this->assertSame(3, $modelo->status_id);
        $this->assertSame(4, $modelo->quadro_id);
        $this->assertSame('Desc', $modelo->descricao);
    }
}
