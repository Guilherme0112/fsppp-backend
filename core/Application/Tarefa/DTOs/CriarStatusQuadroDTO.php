<?php

namespace Core\Application\Tarefa\DTOs;

use Core\Domain\Tarefa\Entities\StatusQuadro;

readonly class CriarStatusQuadroDTO
{
    public function __construct(public int $quadroId, public int $usuarioId, public string $nome, public ?string $descricao = null) {}

    public static function deArray(array $dados, int $quadroId, int $usuarioId): self
    {
        return new self($quadroId, $usuarioId, $dados['nome'], $dados['descricao'] ?? null);
    }

    public function paraEntidade(): StatusQuadro
    {
        return new StatusQuadro(null, $this->nome, $this->quadroId, $this->descricao);
    }
}
