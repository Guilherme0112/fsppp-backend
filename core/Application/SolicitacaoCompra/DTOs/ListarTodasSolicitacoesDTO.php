<?php

namespace Core\Application\SolicitacaoCompra\DTOs;

readonly class ListarTodasSolicitacoesDTO
{
    public function __construct(
        public int $pagina = 1,
        public int $porPagina = 15,
        public ?string $status = null,
        public ?int $usuarioId = null,
        public ?string $busca = null,
    ) {}

    public static function deArray(array $dados): self
    {
        return new self(
            pagina: (int) ($dados['page'] ?? 1),
            porPagina: (int) ($dados['per_page'] ?? 15),
            status: $dados['status'] ?? null,
            usuarioId: isset($dados['usuario_id']) ? (int) $dados['usuario_id'] : null,
            busca: $dados['busca'] ?? null,
        );
    }

    /** @return array{status: ?string, usuario_id: ?int, busca: ?string} */
    public function paraFiltros(): array
    {
        return ['status' => $this->status, 'usuario_id' => $this->usuarioId, 'busca' => $this->busca];
    }
}
