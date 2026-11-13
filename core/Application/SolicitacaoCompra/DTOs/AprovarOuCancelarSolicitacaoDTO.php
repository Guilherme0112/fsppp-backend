<?php

namespace Core\Application\SolicitacaoCompra\DTOs;

readonly class AprovarOuCancelarSolicitacaoDTO
{
    public function __construct(
        public int $id,
        public string $status,
        public int $usuarioId,
    ) {}

    public static function deArray(array $dados, int $id, int $usuarioId): self
    {
        return new self(
            id: $id,
            status: $dados['status'],
            usuarioId: $usuarioId,
        );
    }
}
