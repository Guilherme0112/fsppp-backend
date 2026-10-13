<?php

namespace Core\Application\Tarefa\UseCases;

use Core\Application\Tarefa\DTOs\ListarTarefasDTO;
use Core\Domain\Tarefa\Exceptions\RecursoNaoEncontradoException;
use Core\Domain\Tarefa\Repositories\QuadroRepository;
use Core\Domain\Tarefa\Repositories\TarefaRepository;

class ListarTarefasUseCase
{
    public function __construct(
        private readonly TarefaRepository $tarefaRepository,
        private readonly QuadroRepository $quadroRepository,
    ) {}

    public function executar(ListarTarefasDTO $dto): array
    {
        if (! $this->quadroRepository->pertenceAoUsuario($dto->quadroId, $dto->usuarioId)) {
            throw new RecursoNaoEncontradoException;
        }

        return $this->tarefaRepository->listarAgrupadoPorStatusDoQuadro($dto->quadroId);
    }
}
