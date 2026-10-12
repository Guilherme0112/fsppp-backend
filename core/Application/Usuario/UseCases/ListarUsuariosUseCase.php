<?php

namespace Core\Application\Usuario\UseCases;

use Core\Application\Usuario\DTOs\ListarUsuariosDTO;
use Core\Domain\Usuario\Repositories\UsuarioRepository;

class ListarUsuariosUseCase
{
    public function __construct(private readonly UsuarioRepository $usuarioRepository) {}

    public function executar(ListarUsuariosDTO $dto): array
    {
        return $this->usuarioRepository->listar();
    }
}
