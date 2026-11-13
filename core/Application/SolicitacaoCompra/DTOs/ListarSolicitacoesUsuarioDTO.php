<?php

namespace Core\Application\SolicitacaoCompra\DTOs;

readonly class ListarSolicitacoesUsuarioDTO
{
    public function __construct(
        public int $usuarioId,
        public int $pagina = 1,
        public int $porPagina = 15,
        public ?string $status = null,
        public ?string $busca = null,
    ) {}

    public static function deArray(array $dados, int $usuarioId): self
    {
        return new self(
            usuarioId: $usuarioId,
            pagina: (int) ($dados['page'] ?? 1),
            porPagina: (int) ($dados['per_page'] ?? 15),
            status: $dados['status'] ?? null,
            busca: $dados['busca'] ?? null,
        );
    }

    /** @return array{status: ?string, busca: ?string} */
    public function paraFiltros(): array
    {
        return ['status' => $this->status, 'busca' => $this->busca];
    }
}
