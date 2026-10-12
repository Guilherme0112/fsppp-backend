<?php

namespace Tests\Unit\Domain\Tarefa;

use Core\Domain\Tarefa\Entities\StatusQuadro;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class StatusQuadroTest extends TestCase
{
    public function test_cria_entidade_status_quadro_com_todos_os_campos(): void
    {
        $criadoEm = new DateTimeImmutable('2026-02-01 08:00:00');
        $atualizadoEm = new DateTimeImmutable('2026-02-02 09:00:00');
        $apagadoEm = new DateTimeImmutable('2026-02-03 10:00:00');

        $status = new StatusQuadro(
            id: 2,
            nome: 'Em Progresso',
            quadro_id: 10,
            descricao: 'Itens em desenvolvimento',
            criado_em: $criadoEm,
            atualizado_em: $atualizadoEm,
            apagado_em: $apagadoEm,
        );

        $this->assertSame(2, $status->id());
        $this->assertSame('Em Progresso', $status->nome());
        $this->assertSame(10, $status->quadroId());
        $this->assertSame('Itens em desenvolvimento', $status->descricao());
        $this->assertSame($criadoEm, $status->criadoEm());
        $this->assertSame($atualizadoEm, $status->atualizadoEm());
        $this->assertSame($apagadoEm, $status->apagadoEm());
    }

    public function test_cria_entidade_status_quadro_com_campos_opcionais_nulos(): void
    {
        $status = new StatusQuadro(
            id: null,
            nome: 'A Fazer',
            quadro_id: 5,
        );

        $this->assertNull($status->id());
        $this->assertSame('A Fazer', $status->nome());
        $this->assertSame(5, $status->quadroId());
        $this->assertNull($status->descricao());
        $this->assertNull($status->criadoEm());
        $this->assertNull($status->atualizadoEm());
        $this->assertNull($status->apagadoEm());
    }

    public function test_converte_para_array_e_json_serialize(): void
    {
        $criadoEm = new DateTimeImmutable('2026-02-01T08:00:00+00:00');
        $status = new StatusQuadro(
            id: 3,
            nome: 'Concluído',
            quadro_id: 12,
            descricao: 'Finalizado',
            criado_em: $criadoEm,
        );

        $array = $status->paraArray();

        $this->assertSame([
            'id' => 3,
            'nome' => 'Concluído',
            'quadro_id' => 12,
            'descricao' => 'Finalizado',
            'criado_em' => $criadoEm->format(DATE_ATOM),
            'atualizado_em' => null,
            'apagado_em' => null,
        ], $array);

        $this->assertSame($array, $status->jsonSerialize());
        $this->assertJson(json_encode($status));
    }
}
