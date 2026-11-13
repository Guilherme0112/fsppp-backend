<?php

namespace Core\Application\Tarefa\UseCases;

use Core\Domain\Tarefa\Repositories\QuadroRepository;

class ListarUsuariosCompartilhamentoUseCase
{
    public function __construct(private readonly QuadroRepository $quadroRepository) {}

    public function executar(int $pagina, int $porPagina, ?string $busca): array
    {
        return $this->quadroRepository->listarUsuariosParaCompartilhamento($pagina, $porPagina, $busca);
    }
}
