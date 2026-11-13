<?php

namespace Core\Application\SolicitacaoCompra\DTOs;

readonly class AtualizarStatusCompraDTO
{
    public function __construct(
        public int $id,
        public string $statusCompra,
        public int $usuarioId,
    ) {}

    public static function deArray(array $dados, int $id, int $usuarioId): self
    {
        return new self(
            id: $id,
            statusCompra: $dados['status_compra'],
            usuarioId: $usuarioId,
        );
    }
}
