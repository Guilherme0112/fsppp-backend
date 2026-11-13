<?php

namespace Core\Application\SolicitacaoCompra\UseCases;

use Core\Application\SolicitacaoCompra\DTOs\ListarTodasSolicitacoesDTO;
use Core\Domain\SolicitacaoCompra\Repositories\SolicitacaoCompraRepository;

class ListarTodasSolicitacoesUseCase
{
    public function __construct(private readonly SolicitacaoCompraRepository $repositorio) {}

    public function executar(ListarTodasSolicitacoesDTO $dto): array
    {
        return $this->repositorio->listarTodas($dto->paraFiltros(), $dto->pagina, $dto->porPagina);
    }
}
