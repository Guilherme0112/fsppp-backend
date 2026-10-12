<?php

namespace Tests\Unit\Domain\Tarefa;

use Core\Domain\Tarefa\Entities\Tarefa;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class TarefaTest extends TestCase
{
    public function test_cria_entidade_tarefa_com_todos_os_campos(): void
    {
        $dataInicio = new DateTimeImmutable('2026-03-01 09:00:00');
        $dataFim = new DateTimeImmutable('2026-03-05 18:00:00');
        $criadoEm = new DateTimeImmutable('2026-02-28 10:00:00');
        $atualizadoEm = new DateTimeImmutable('2026-03-01 08:30:00');
        $apagadoEm = new DateTimeImmutable('2026-03-10 12:00:00');

        $tarefa = new Tarefa(
            id: 10,
            nome: 'Implementar Testes',
            status_id: 2,
            quadro_id: 1,
            descricao: 'Criar testes unitários e de integração',
            data_inicio: $dataInicio,
            data_fim: $dataFim,
            criado_em: $criadoEm,
            atualizado_em: $atualizadoEm,
            apagado_em: $apagadoEm,
        );

        $this->assertSame(10, $tarefa->id());
        $this->assertSame('Implementar Testes', $tarefa->nome());
        $this->assertSame(2, $tarefa->statusId());
        $this->assertSame(1, $tarefa->quadroId());
        $this->assertSame('Criar testes unitários e de integração', $tarefa->descricao());
        $this->assertSame($dataInicio, $tarefa->dataInicio());
        $this->assertSame($dataFim, $tarefa->dataFim());
        $this->assertSame($criadoEm, $tarefa->criadoEm());
        $this->assertSame($atualizadoEm, $tarefa->atualizadoEm());
        $this->assertSame($apagadoEm, $tarefa->apagadoEm());
    }

    public function test_cria_entidade_tarefa_com_campos_opcionais_nulos(): void
    {
        $tarefa = new Tarefa(
            id: null,
            nome: 'Tarefa Simples',
            status_id: 1,
            quadro_id: 1,
        );

        $this->assertNull($tarefa->id());
        $this->assertSame('Tarefa Simples', $tarefa->nome());
        $this->assertSame(1, $tarefa->statusId());
        $this->assertSame(1, $tarefa->quadroId());
        $this->assertNull($tarefa->descricao());
        $this->assertNull($tarefa->dataInicio());
        $this->assertNull($tarefa->dataFim());
        $this->assertNull($tarefa->criadoEm());
        $this->assertNull($tarefa->atualizadoEm());
        $this->assertNull($tarefa->apagadoEm());
    }

    public function test_converte_para_array_e_json_serialize(): void
    {
        $dataInicio = new DateTimeImmutable('2026-03-01T09:00:00+00:00');
        $dataFim = new DateTimeImmutable('2026-03-05T18:00:00+00:00');
        $criadoEm = new DateTimeImmutable('2026-02-28T10:00:00+00:00');

        $tarefa = new Tarefa(
            id: 7,
            nome: 'Tarefa Teste JSON',
            status_id: 3,
            quadro_id: 4,
            descricao: 'Desc',
            data_inicio: $dataInicio,
            data_fim: $dataFim,
            criado_em: $criadoEm,
        );

        $array = $tarefa->paraArray();

        $this->assertSame([
            'id' => 7,
            'nome' => 'Tarefa Teste JSON',
            'status_id' => 3,
            'quadro_id' => 4,
            'descricao' => 'Desc',
            'data_inicio' => $dataInicio->format(DATE_ATOM),
            'data_fim' => $dataFim->format(DATE_ATOM),
            'criado_em' => $criadoEm->format(DATE_ATOM),
            'atualizado_em' => null,
            'apagado_em' => null,
        ], $array);

        $this->assertSame($array, $tarefa->jsonSerialize());
        $this->assertJson(json_encode($tarefa));
    }
}
