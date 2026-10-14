<?php

namespace Core\Application\SolicitacaoPagamento\DTOs;

readonly class ConcluirSolicitacaoDTO
{
    public function __construct(
        public int $id,
        public int $usuarioId,
    ) {}
}
