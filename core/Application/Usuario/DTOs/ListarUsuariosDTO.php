<?php

namespace Core\Application\Usuario\DTOs;

readonly class ListarUsuariosDTO
{
    public function __construct(
        public int $usuarioId,
        public int $pagina = 1,
        public int $porPagina = 20,
    ) {}

    public static function daRequisicao(array $dados, int $usuarioId): self
    {
        return new self(
            usuarioId: $usuarioId,
            pagina: $dados['page'] ?? 1,
            porPagina: $dados['per_page'] ?? 20,
        );
    }
}
