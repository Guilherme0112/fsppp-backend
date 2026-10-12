<?php

namespace Core\Application\Usuario\DTOs;

readonly class ListarUsuariosDTO
{
    public function __construct(public int $usuarioId) {}

    public static function daRequisicao(array $dados, int $usuarioId): self
    {
        return new self(usuarioId: $usuarioId);
    }
}
