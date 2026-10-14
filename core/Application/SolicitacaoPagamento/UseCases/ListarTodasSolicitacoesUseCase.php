<?php

namespace Core\Application\SolicitacaoPagamento\UseCases;

use Core\Application\SolicitacaoPagamento\DTOs\ListarTodasSolicitacoesDTO;
use Core\Domain\SolicitacaoPagamento\Repositories\SolicitacaoPagamentoRepository;

class ListarTodasSolicitacoesUseCase
{
    public function __construct(private readonly SolicitacaoPagamentoRepository $repositorio) {}

    public function executar(ListarTodasSolicitacoesDTO $dto): array
    {
        return $this->repositorio->listarTodas($dto->paraFiltros(), $dto->pagina, $dto->porPagina);
    }
}
