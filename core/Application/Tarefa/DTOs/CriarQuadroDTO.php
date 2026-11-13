<?php

namespace Core\Application\Tarefa\DTOs;

use Core\Domain\Tarefa\Entities\Quadro;

readonly class CriarQuadroDTO
{
    public function __construct(
        public string $nome,
        public int $usuarioId,
        public ?string $descricao = null,
    ) {}

    public static function deArray(array $dados, int $usuarioId): self
    {
        return new self($dados['nome'], $usuarioId, $dados['descricao'] ?? null);
    }

    public function paraEntidade(): Quadro
    {
        return new Quadro(null, $this->nome, $this->usuarioId, $this->descricao);
    }
}
