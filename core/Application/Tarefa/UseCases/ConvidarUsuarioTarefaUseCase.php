<?php

namespace Core\Application\Tarefa\UseCases;

use Core\Domain\Tarefa\Exceptions\RecursoNaoEncontradoException;
use Core\Domain\Tarefa\Repositories\TarefaRepository;

class ConvidarUsuarioTarefaUseCase
{
    public function __construct(private readonly TarefaRepository $tarefaRepository) {}

    public function executar(int $tarefaId, int $usuarioId, int $autenticadoId): void
    {
        if (! $this->tarefaRepository->pertenceAoUsuario($tarefaId, $autenticadoId)) {
            throw new RecursoNaoEncontradoException;
        }

        $this->tarefaRepository->convidarUmUsuario($tarefaId, $usuarioId);
    }
}
