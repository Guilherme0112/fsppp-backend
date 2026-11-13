<?php

namespace Core\Application\SolicitacaoCompra\DTOs;

readonly class AvaliarProjetosDTO
{
    public function __construct(
        public int $id,
        public bool $recursosExistem,
        public int $usuarioId,
    ) {}

    public static function deArray(array $dados, int $id, int $usuarioId): self
    {
        return new self(
            id: $id,
            recursosExistem: (bool) ($dados['recursos_existem'] ?? false),
            usuarioId: $usuarioId,
        );
    }
}
