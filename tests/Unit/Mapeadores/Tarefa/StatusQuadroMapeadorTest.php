<?php

namespace Tests\Unit\Mapeadores\Tarefa;

use App\Mapeadores\Tarefa\StatusQuadroMapeador;
use App\Models\Tarefa\StatusQuadro as StatusQuadroModelo;
use Core\Domain\Tarefa\Entities\StatusQuadro as StatusQuadroEntidade;
use DateTimeImmutable;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class StatusQuadroMapeadorTest extends TestCase
{
    private StatusQuadroMapeador $mapeador;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mapeador = new StatusQuadroMapeador;
    }

    public function test_para_entidade_mapeia_todos_os_atributos(): void
    {
        $modelo = new StatusQuadroModelo;
        $modelo->id = 5;
        $modelo->nome = 'A Fazer';
        $modelo->quadro_id = 15;
        $modelo->descricao = 'Status desc';
        $modelo->criado_em = Carbon::parse('2026-02-01 10:00:00');
        $modelo->atualizado_em = Carbon::parse('2026-02-02 11:00:00');
        $modelo->apagado_em = null;

        $entidade = $this->mapeador->paraEntidade($modelo);

        $this->assertSame(5, $entidade->id());
        $this->assertSame('A Fazer', $entidade->nome());
        $this->assertSame(15, $entidade->quadroId());
        $this->assertSame('Status desc', $entidade->descricao());
        $this->assertEquals(new DateTimeImmutable('2026-02-01 10:00:00'), $entidade->criadoEm());
        $this->assertEquals(new DateTimeImmutable('2026-02-02 11:00:00'), $entidade->atualizadoEm());
        $this->assertNull($entidade->apagadoEm());
    }

    public function test_para_modelo_mapeia_entidade_para_modelo(): void
    {
        $entidade = new StatusQuadroEntidade(
            id: 8,
            nome: 'Em Progresso',
            quadro_id: 15,
            descricao: 'Em andamento',
        );

        $modelo = $this->mapeador->paraModelo($entidade);

        $this->assertSame(8, $modelo->getKey());
        $this->assertSame('Em Progresso', $modelo->nome);
        $this->assertSame(15, $modelo->quadro_id);
        $this->assertSame('Em andamento', $modelo->descricao);
    }
}
