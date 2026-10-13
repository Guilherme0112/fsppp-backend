<?php

namespace Core\Application\Tarefa\DTOs;

readonly class ListarTarefasDTO
{
    public function __construct(public int $usuarioId, public int $quadroId) {}

    public static function daRequisicao(array $dados, int $usuarioId): self
    {
        return new self(usuarioId: $usuarioId, quadroId: (int) $dados['quadro_id']);
    }
}
