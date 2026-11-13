<?php

namespace Core\Application\Tarefa\DTOs;

use Core\Domain\Tarefa\Entities\Quadro;

readonly class AtualizarQuadroDTO
{
    public function __construct(
        public int $id,
        public string $nome,
        public int $usuarioId,
        public ?string $descricao = null,
    ) {}

    public static function deArray(array $dados, int $id, int $usuarioId): self
    {
        return new self($id, $dados['nome'], $usuarioId, $dados['descricao'] ?? null);
    }

    public function paraEntidade(): Quadro
    {
        return new Quadro($this->id, $this->nome, $this->usuarioId, $this->descricao);
    }
}
