<?php

namespace Core\Application\SolicitacaoCompra\DTOs;

readonly class AvaliarDiretoriaDTO
{
    public function __construct(
        public int $id,
        public bool $aprovado,
        public string $prioridade,
        public ?string $motivoRejeicao,
        public int $usuarioId,
    ) {}

    public static function deArray(array $dados, int $id, int $usuarioId): self
    {
        return new self(
            id: $id,
            aprovado: (bool) ($dados['aprovado'] ?? false),
            prioridade: $dados['prioridade'] ?? 'media',
            motivoRejeicao: $dados['motivo_rejeicao'] ?? null,
            usuarioId: $usuarioId,
        );
    }
}
