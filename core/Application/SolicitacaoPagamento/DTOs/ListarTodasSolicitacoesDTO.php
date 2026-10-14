<?php

namespace Core\Application\SolicitacaoPagamento\DTOs;

readonly class ListarTodasSolicitacoesDTO
{
    public function __construct(
        public int $pagina = 1,
        public int $porPagina = 15,
        public ?string $status = null,
        public ?int $usuarioId = null,
    ) {}

    public static function daRequisicao(array $dados): self
    {
        return new self(
            pagina: (int) ($dados['page'] ?? 1),
            porPagina: (int) ($dados['per_page'] ?? 15),
            status: $dados['status'] ?? null,
            usuarioId: isset($dados['usuario_id']) ? (int) $dados['usuario_id'] : null,
        );
    }

    /** @return array{status: ?string, usuario_id: ?int} */
    public function paraFiltros(): array
    {
        return ['status' => $this->status, 'usuario_id' => $this->usuarioId];
    }
}
