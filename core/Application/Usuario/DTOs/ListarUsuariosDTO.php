<?php

namespace Core\Application\Usuario\DTOs;

readonly class ListarUsuariosDTO
{
    public function __construct(
        public int $usuarioId,
        public int $pagina = 1,
        public int $porPagina = 20,
        public ?string $busca = null,
    ) {}

    public static function deArray(array $dados, int $usuarioId): self
    {
        $busca = isset($dados['search']) ? trim((string) $dados['search']) : null;

        return new self(
            usuarioId: $usuarioId,
            pagina: $dados['page'] ?? 1,
            porPagina: $dados['per_page'] ?? 20,
            busca: $busca !== '' ? $busca : null,
        );
    }
}
