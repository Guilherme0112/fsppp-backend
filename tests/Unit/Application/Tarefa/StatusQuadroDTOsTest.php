<?php

namespace Tests\Unit\Application\Tarefa;

use Core\Application\Tarefa\DTOs\AtualizarStatusQuadroDTO;
use Core\Application\Tarefa\DTOs\CriarStatusQuadroDTO;
use PHPUnit\Framework\TestCase;

class StatusQuadroDTOsTest extends TestCase
{
    public function test_criar_status_quadro_dto_da_requisicao_e_para_entidade(): void
    {
        $dados = [
            'nome' => 'Em Análise',
            'descricao' => 'Itens sob revisão',
        ];

        $dto = CriarStatusQuadroDTO::daRequisicao($dados, 1, 10);

        $this->assertSame(1, $dto->quadroId);
        $this->assertSame(10, $dto->usuarioId);
        $this->assertSame('Em Análise', $dto->nome);
        $this->assertSame('Itens sob revisão', $dto->descricao);

        $entidade = $dto->paraEntidade();
        $this->assertNull($entidade->id());
        $this->assertSame('Em Análise', $entidade->nome());
        $this->assertSame(1, $entidade->quadroId());
        $this->assertSame('Itens sob revisão', $entidade->descricao());
    }

    public function test_atualizar_status_quadro_dto_da_requisicao_e_para_entidade(): void
    {
        $dados = [
            'nome' => 'Pronto',
            'descricao' => null,
        ];

        $dto = AtualizarStatusQuadroDTO::daRequisicao($dados, 2, 7, 10);

        $this->assertSame(7, $dto->id);
        $this->assertSame(2, $dto->quadroId);
        $this->assertSame(10, $dto->usuarioId);
        $this->assertSame('Pronto', $dto->nome);
        $this->assertNull($dto->descricao);

        $entidade = $dto->paraEntidade();
        $this->assertSame(7, $entidade->id());
        $this->assertSame('Pronto', $entidade->nome());
        $this->assertSame(2, $entidade->quadroId());
        $this->assertNull($entidade->descricao());
    }
}
