<?php

namespace Core\Application\Tarefa\DTOs;

use Core\Domain\Tarefa\Entities\StatusQuadro;

readonly class AtualizarStatusQuadroDTO
{
    public function __construct(public int $id, public int $quadroId, public int $usuarioId, public string $nome, public ?string $descricao = null) {}

    public static function deArray(array $dados, int $quadroId, int $id, int $usuarioId): self
    {
        return new self($id, $quadroId, $usuarioId, $dados['nome'], $dados['descricao'] ?? null);
    }

    public function paraEntidade(): StatusQuadro
    {
        return new StatusQuadro($this->id, $this->nome, $this->quadroId, $this->descricao);
    }
}
