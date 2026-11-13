<?php

namespace Core\Application\SolicitacaoCompra\DTOs;

readonly class EnviarParaDiretoriaDTO
{
    public function __construct(
        public int $id,
        public int $usuarioId,
    ) {}

    public static function deArray(array $dados, int $id, int $usuarioId): self
    {
        return new self(
            id: $id,
            usuarioId: $usuarioId,
        );
    }
}
