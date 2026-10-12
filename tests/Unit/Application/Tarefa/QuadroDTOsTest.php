<?php

namespace Tests\Unit\Application\Tarefa;

use Core\Application\Tarefa\DTOs\AtualizarQuadroDTO;
use Core\Application\Tarefa\DTOs\CriarQuadroDTO;
use Core\Application\Tarefa\DTOs\ListarQuadrosDTO;
use PHPUnit\Framework\TestCase;

class QuadroDTOsTest extends TestCase
{
    public function test_criar_quadro_dto_da_requisicao_e_para_entidade(): void
    {
        $dados = [
            'nome' => 'Novo Quadro',
            'descricao' => 'Descrição opcional',
        ];

        $dto = CriarQuadroDTO::daRequisicao($dados, 10);

        $this->assertSame('Novo Quadro', $dto->nome);
        $this->assertSame(10, $dto->usuarioId);
        $this->assertSame('Descrição opcional', $dto->descricao);

        $entidade = $dto->paraEntidade();
        $this->assertNull($entidade->id());
        $this->assertSame('Novo Quadro', $entidade->nome());
        $this->assertSame(10, $entidade->usuarioId());
        $this->assertSame('Descrição opcional', $entidade->descricao());
    }

    public function test_atualizar_quadro_dto_da_requisicao_e_para_entidade(): void
    {
        $dados = [
            'nome' => 'Quadro Modificado',
            'descricao' => null,
        ];

        $dto = AtualizarQuadroDTO::daRequisicao($dados, 5, 10);

        $this->assertSame(5, $dto->id);
        $this->assertSame('Quadro Modificado', $dto->nome);
        $this->assertSame(10, $dto->usuarioId);
        $this->assertNull($dto->descricao);

        $entidade = $dto->paraEntidade();
        $this->assertSame(5, $entidade->id());
        $this->assertSame('Quadro Modificado', $entidade->nome());
        $this->assertSame(10, $entidade->usuarioId());
        $this->assertNull($entidade->descricao());
    }

    public function test_listar_quadros_dto_da_requisicao(): void
    {
        $dto = ListarQuadrosDTO::daRequisicao([], 42);

        $this->assertSame(42, $dto->usuarioId);
    }
}
