<?php

namespace Tests\Unit\Domain\Tarefa;

use Core\Domain\Tarefa\Entities\Quadro;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class QuadroTest extends TestCase
{
    public function test_cria_entidade_quadro_com_todos_os_campos(): void
    {
        $criadoEm = new DateTimeImmutable('2026-01-01 10:00:00');
        $atualizadoEm = new DateTimeImmutable('2026-01-02 11:00:00');
        $apagadoEm = new DateTimeImmutable('2026-01-03 12:00:00');

        $quadro = new Quadro(
            id: 1,
            nome: 'Projeto Alpha',
            usuario_id: 10,
            descricao: 'Descrição do projeto',
            criado_em: $criadoEm,
            atualizado_em: $atualizadoEm,
            apagado_em: $apagadoEm,
        );

        $this->assertSame(1, $quadro->id());
        $this->assertSame('Projeto Alpha', $quadro->nome());
        $this->assertSame(10, $quadro->usuarioId());
        $this->assertSame('Descrição do projeto', $quadro->descricao());
        $this->assertSame($criadoEm, $quadro->criadoEm());
        $this->assertSame($atualizadoEm, $quadro->atualizadoEm());
        $this->assertSame($apagadoEm, $quadro->apagadoEm());
    }

    public function test_cria_entidade_quadro_com_campos_opcionais_nulos(): void
    {
        $quadro = new Quadro(
            id: null,
            nome: 'Projeto Beta',
            usuario_id: 20,
        );

        $this->assertNull($quadro->id());
        $this->assertSame('Projeto Beta', $quadro->nome());
        $this->assertSame(20, $quadro->usuarioId());
        $this->assertNull($quadro->descricao());
        $this->assertNull($quadro->criadoEm());
        $this->assertNull($quadro->atualizadoEm());
        $this->assertNull($quadro->apagadoEm());
    }

    public function test_converte_para_array_e_json_serialize(): void
    {
        $criadoEm = new DateTimeImmutable('2026-01-01T10:00:00+00:00');
        $quadro = new Quadro(
            id: 5,
            nome: 'Quadro JSON',
            usuario_id: 15,
            descricao: 'Teste JSON',
            criado_em: $criadoEm,
        );

        $array = $quadro->paraArray();

        $this->assertSame([
            'id' => 5,
            'nome' => 'Quadro JSON',
            'usuario_id' => 15,
            'descricao' => 'Teste JSON',
            'criado_em' => $criadoEm->format(DATE_ATOM),
            'atualizado_em' => null,
            'apagado_em' => null,
        ], $array);

        $this->assertSame($array, $quadro->jsonSerialize());
        $this->assertJson(json_encode($quadro));
    }
}
