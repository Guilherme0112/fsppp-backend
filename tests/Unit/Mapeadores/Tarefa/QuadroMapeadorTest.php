<?php

namespace Tests\Unit\Mapeadores\Tarefa;

use App\Mapeadores\Tarefa\QuadroMapeador;
use App\Models\Tarefa\Quadro as QuadroModelo;
use Core\Domain\Tarefa\Entities\Quadro as QuadroEntidade;
use DateTimeImmutable;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class QuadroMapeadorTest extends TestCase
{
    private QuadroMapeador $mapeador;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mapeador = new QuadroMapeador;
    }

    public function test_para_entidade_mapeia_todos_os_atributos(): void
    {
        $modelo = new QuadroModelo;
        $modelo->id = 1;
        $modelo->nome = 'Quadro Teste';
        $modelo->usuario_id = 10;
        $modelo->descricao = 'Desc';
        $modelo->criado_em = Carbon::parse('2026-01-01 12:00:00');
        $modelo->atualizado_em = Carbon::parse('2026-01-02 12:00:00');
        $modelo->apagado_em = null;

        $entidade = $this->mapeador->paraEntidade($modelo);

        $this->assertSame(1, $entidade->id());
        $this->assertSame('Quadro Teste', $entidade->nome());
        $this->assertSame(10, $entidade->usuarioId());
        $this->assertSame('Desc', $entidade->descricao());
        $this->assertEquals(new DateTimeImmutable('2026-01-01 12:00:00'), $entidade->criadoEm());
        $this->assertEquals(new DateTimeImmutable('2026-01-02 12:00:00'), $entidade->atualizadoEm());
        $this->assertNull($entidade->apagadoEm());
    }

    public function test_para_modelo_mapeia_entidade_para_novo_modelo(): void
    {
        $criadoEm = new DateTimeImmutable('2026-01-01 12:00:00');
        $entidade = new QuadroEntidade(
            id: 2,
            nome: 'Quadro Modelo',
            usuario_id: 20,
            descricao: 'Nova desc',
            criado_em: $criadoEm,
        );

        $modelo = $this->mapeador->paraModelo($entidade);

        $this->assertSame(2, $modelo->getKey());
        $this->assertSame('Quadro Modelo', $modelo->nome);
        $this->assertSame(20, $modelo->usuario_id);
        $this->assertSame('Nova desc', $modelo->descricao);
        $this->assertEquals($criadoEm, $modelo->criado_em);
    }
}
