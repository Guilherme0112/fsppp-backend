<?php

namespace Core\Application\Tarefa\DTOs;

readonly class ListarTarefasPorStatusDTO
{
    public function __construct(
        public int $usuarioId,
        public int $statusId,
        public int $pagina,
    ) {}

    public static function daRequisicao(array $dados, int $statusId, int $usuarioId): self
    {
        return new self(
            usuarioId: $usuarioId,
            statusId: $statusId,
            pagina: (int) ($dados['page'] ?? 1),
        );
    }
}
