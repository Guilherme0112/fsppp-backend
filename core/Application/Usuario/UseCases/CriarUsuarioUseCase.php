<?php

namespace Core\Application\Usuario\UseCases;

use Core\Application\Usuario\DTOs\CriarUsuarioDTO;
use Core\Domain\Usuario\Entities\Usuario;
use Core\Domain\Usuario\Repositories\UsuarioRepository;

class CriarUsuarioUseCase
{
    public function __construct(private readonly UsuarioRepository $usuarioRepository) {}

    public function executar(CriarUsuarioDTO $dto): Usuario
    {
        return $this->usuarioRepository->criar($dto->paraEntidade());
    }
}
