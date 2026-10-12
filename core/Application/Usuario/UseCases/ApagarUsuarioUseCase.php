<?php

namespace Core\Application\Usuario\UseCases;

use Core\Domain\Usuario\Repositories\UsuarioRepository;

class ApagarUsuarioUseCase
{
    public function __construct(private readonly UsuarioRepository $usuarioRepository) {}

    public function executar(int $id): void
    {
        $this->usuarioRepository->apagar($id);
    }
}
