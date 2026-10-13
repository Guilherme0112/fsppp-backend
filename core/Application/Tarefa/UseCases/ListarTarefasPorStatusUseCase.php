<?php

namespace Core\Application\Tarefa\UseCases;

use Core\Application\Tarefa\DTOs\ListarTarefasPorStatusDTO;
use Core\Domain\Tarefa\Exceptions\RecursoNaoEncontradoException;
use Core\Domain\Tarefa\Repositories\StatusQuadroRepository;
use Core\Domain\Tarefa\Repositories\TarefaRepository;

class ListarTarefasPorStatusUseCase
{
    public function __construct(
        private readonly TarefaRepository $tarefaRepository,
        private readonly StatusQuadroRepository $statusQuadroRepository,
    ) {}

    public function executar(ListarTarefasPorStatusDTO $dto): array
    {
        if (! $this->statusQuadroRepository->pertenceAoUsuario($dto->statusId, $dto->usuarioId)) {
            throw new RecursoNaoEncontradoException;
        }

        return $this->tarefaRepository->listarPorStatus($dto->statusId, $dto->pagina);
    }
}
