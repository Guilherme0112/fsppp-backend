<?php

namespace Core\Domain\Tarefa\Repositories;

use Core\Domain\Tarefa\Entities\Quadro;

interface QuadroRepository
{
    public function listarPorUsuarioId(int $usuarioId): array;

    public function criar(Quadro $quadro): Quadro;

    public function atualizar(int $id, Quadro $quadro): Quadro;

    public function apagar(int $id): void;

    public function pertenceAoUsuario(int $id, int $usuarioId): bool;

    public function convidarUmUsuario(int $quadroId, int $usuarioId): void;
}
