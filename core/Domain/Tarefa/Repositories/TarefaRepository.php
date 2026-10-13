<?php

namespace Core\Domain\Tarefa\Repositories;

use Core\Domain\Tarefa\Entities\Tarefa;

interface TarefaRepository
{
    public function listarPorQuadroId(int $quadroId): array;

    public function listarPorUsuarioId(int $usuarioId): array;

    public function listarAgrupadoPorStatusDoQuadro(int $quadroId, int $limitePorStatus = 10): array;

    public function listarPorStatus(int $statusId, int $pagina, int $porPagina = 10): array;

    public function criar(int $quadroId, Tarefa $tarefa): Tarefa;

    public function atualizar(int $id, Tarefa $tarefa): Tarefa;

    public function apagar(int $id): void;

    public function pertenceAoUsuario(int $id, int $usuarioId): bool;
}
