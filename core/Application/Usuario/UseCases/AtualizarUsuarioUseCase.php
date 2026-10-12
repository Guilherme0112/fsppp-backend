<?php

namespace Core\Application\Usuario\UseCases;

use Core\Application\Usuario\DTOs\AtualizarUsuarioDTO;
use Core\Domain\Usuario\Entities\Usuario;
use Core\Domain\Usuario\Repositories\UsuarioRepository;

class AtualizarUsuarioUseCase
{
    public function __construct(private readonly UsuarioRepository $usuarioRepository) {}

    public function executar(AtualizarUsuarioDTO $dto): Usuario
    {
        return $this->usuarioRepository->atualizar($dto->id, $dto->paraEntidade());
    }
}
