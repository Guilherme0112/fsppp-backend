<?php

namespace Core\Application\SolicitacaoCompra\UseCases;

use Core\Application\SolicitacaoCompra\DTOs\CriarSolicitacaoCompraDTO;
use Core\Domain\SolicitacaoCompra\Entities\SolicitacaoCompra;
use Core\Domain\SolicitacaoCompra\Repositories\SolicitacaoCompraRepository;

class CriarSolicitacaoCompraUseCase
{
    public function __construct(private readonly SolicitacaoCompraRepository $repositorio) {}

    public function executar(CriarSolicitacaoCompraDTO $dto): SolicitacaoCompra
    {
        return $this->repositorio->criar($dto->paraEntidade());
    }
}
