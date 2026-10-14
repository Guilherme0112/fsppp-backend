<?php

namespace Core\Application\SolicitacaoPagamento\DTOs;

readonly class ListarSolicitacoesUsuarioDTO
{
    public function __construct(
        public int $usuarioId,
        public int $pagina = 1,
        public int $porPagina = 15,
    ) {}

    public static function daRequisicao(array $dados, int $usuarioId): self
    {
        return new self(
            usuarioId: $usuarioId,
            pagina: (int) ($dados['page'] ?? 1),
            porPagina: (int) ($dados['per_page'] ?? 15),
        );
    }
}
