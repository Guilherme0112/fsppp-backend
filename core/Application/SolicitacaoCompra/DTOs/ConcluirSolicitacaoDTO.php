<?php

namespace Core\Application\SolicitacaoCompra\DTOs;

readonly class ConcluirSolicitacaoDTO
{
    public function __construct(
        public int $id,
        public int $usuarioId,
    ) {}
}
