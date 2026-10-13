<?php

namespace Tests\Unit\Application\Tarefa;

use Core\Application\Tarefa\DTOs\AtualizarTarefaDTO;
use Core\Application\Tarefa\DTOs\CriarTarefaDTO;
use Core\Application\Tarefa\DTOs\ListarTarefasDTO;
use Core\Application\Tarefa\DTOs\ListarTarefasPorStatusDTO;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class TarefaDTOsTest extends TestCase
{
    public function test_criar_tarefa_dto_da_requisicao_e_para_entidade(): void
    {
        $dados = [
            'nome' => 'Criar Dockerfile',
            'status_id' => 1,
            'quadro_id' => 2,
            'descricao' => 'Configurar ambiente container',
            'data_inicio' => '2026-04-01T10:00:00+00:00',
            'data_fim' => '2026-04-02T18:00:00+00:00',
        ];

        $dto = CriarTarefaDTO::daRequisicao($dados, 10);

        $this->assertSame('Criar Dockerfile', $dto->nome);
        $this->assertSame(1, $dto->statusId);
        $this->assertSame(2, $dto->quadroId);
        $this->assertSame(10, $dto->usuarioId);
        $this->assertSame('Configurar ambiente container', $dto->descricao);
        $this->assertEquals(new DateTimeImmutable('2026-04-01T10:00:00+00:00'), $dto->dataInicio);
        $this->assertEquals(new DateTimeImmutable('2026-04-02T18:00:00+00:00'), $dto->dataFim);

        $entidade = $dto->paraEntidade();
        $this->assertNull($entidade->id());
        $this->assertSame('Criar Dockerfile', $entidade->nome());
        $this->assertSame(1, $entidade->statusId());
        $this->assertSame(2, $entidade->quadroId());
        $this->assertSame('Configurar ambiente container', $entidade->descricao());
        $this->assertEquals($dto->dataInicio, $entidade->dataInicio());
        $this->assertEquals($dto->dataFim, $entidade->dataFim());
    }

    public function test_criar_tarefa_dto_sem_datas_e_sem_descricao(): void
    {
        $dados = [
            'nome' => 'Tarefa Rápida',
            'status_id' => 1,
            'quadro_id' => 2,
        ];

        $dto = CriarTarefaDTO::daRequisicao($dados, 10);

        $this->assertNull($dto->descricao);
        $this->assertNull($dto->dataInicio);
        $this->assertNull($dto->dataFim);

        $entidade = $dto->paraEntidade();
        $this->assertNull($entidade->descricao());
        $this->assertNull($entidade->dataInicio());
        $this->assertNull($entidade->dataFim());
    }

    public function test_atualizar_tarefa_dto_da_requisicao_e_para_entidade(): void
    {
        $dados = [
            'nome' => 'Tarefa Atualizada',
            'status_id' => 3,
            'quadro_id' => 2,
            'descricao' => 'Nova descrição',
            'data_inicio' => '2026-05-01',
            'data_fim' => '2026-05-02',
        ];

        $dto = AtualizarTarefaDTO::daRequisicao($dados, 15, 10);

        $this->assertSame(15, $dto->id);
        $this->assertSame('Tarefa Atualizada', $dto->nome);
        $this->assertSame(3, $dto->statusId);
        $this->assertSame(2, $dto->quadroId);
        $this->assertSame(10, $dto->usuarioId);
        $this->assertSame('Nova descrição', $dto->descricao);

        $entidade = $dto->paraEntidade();
        $this->assertSame(15, $entidade->id());
        $this->assertSame('Tarefa Atualizada', $entidade->nome());
        $this->assertSame(3, $entidade->statusId());
        $this->assertSame(2, $entidade->quadroId());
    }

    public function test_listar_tarefas_dto_da_requisicao(): void
    {
        $dto = ListarTarefasDTO::daRequisicao(['quadro_id' => 5], 99);

        $this->assertSame(99, $dto->usuarioId);
        $this->assertSame(5, $dto->quadroId);
    }

    public function test_listar_tarefas_por_status_dto_da_requisicao_assume_pagina_1_por_padrao(): void
    {
        $dto = ListarTarefasPorStatusDTO::daRequisicao([], 7, 99);

        $this->assertSame(99, $dto->usuarioId);
        $this->assertSame(7, $dto->statusId);
        $this->assertSame(1, $dto->pagina);
    }

    public function test_listar_tarefas_por_status_dto_da_requisicao_com_pagina_explicita(): void
    {
        $dto = ListarTarefasPorStatusDTO::daRequisicao(['page' => 3], 7, 99);

        $this->assertSame(3, $dto->pagina);
    }
}
