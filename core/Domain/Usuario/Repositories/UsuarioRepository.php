<?php

namespace Core\Domain\Usuario\Repositories;

use Core\Domain\Usuario\Entities\Usuario;

interface UsuarioRepository
{
    public function listar(): array;
    public function criar(Usuario $usuario): Usuario;
    public function atualizar(int $id, Usuario $usuario): Usuario;
    public function apagar(int $id): void;
}
    