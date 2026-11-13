<?php

namespace Core\Application\Tarefa\DTOs;

readonly class ListarQuadrosDTO
{
    public function __construct(public int $usuarioId) {}

    public static function deArray(array $dados, int $usuarioId): self
    {
        return new self(usuarioId: $usuarioId);
    }
}
