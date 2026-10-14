<?php

namespace Core\Application\SolicitacaoPagamento\DTOs;

readonly class AprovarOuCancelarSolicitacaoDTO
{
    public function __construct(
        public int $id,
        public string $status,
        public int $usuarioId,
    ) {}

    public static function daRequisicao(array $dados, int $id, int $usuarioId): self
    {
        return new self(
            id: $id,
            status: $dados['status'],
            usuarioId: $usuarioId,
        );
    }
}
