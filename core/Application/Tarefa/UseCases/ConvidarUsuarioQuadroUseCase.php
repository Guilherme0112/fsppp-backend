<?php

namespace Core\Application\Tarefa\UseCases;

use Core\Domain\Tarefa\Exceptions\RecursoNaoEncontradoException;
use Core\Domain\Tarefa\Repositories\QuadroRepository;

class ConvidarUsuarioQuadroUseCase
{
    public function __construct(private readonly QuadroRepository $quadroRepository) {}

    public function executar(int $quadroId, int $usuarioId, int $autenticadoId): void
    {
        if (! $this->quadroRepository->pertenceAoUsuario($quadroId, $autenticadoId)) {
            throw new RecursoNaoEncontradoException;
        }

        $this->quadroRepository->convidarUmUsuario($quadroId, $usuarioId);
    }
}
