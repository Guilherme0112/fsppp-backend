<?php

namespace Core\Application\SolicitacaoPagamento\UseCases;

use Core\Application\SolicitacaoPagamento\DTOs\CriarSolicitacaoPagamentoDTO;
use Core\Domain\SolicitacaoPagamento\Entities\SolicitacaoPagamento;
use Core\Domain\SolicitacaoPagamento\Repositories\SolicitacaoPagamentoRepository;

class CriarSolicitacaoPagamentoUseCase
{
    public function __construct(private readonly SolicitacaoPagamentoRepository $repositorio) {}

    public function executar(CriarSolicitacaoPagamentoDTO $dto): SolicitacaoPagamento
    {
        return $this->repositorio->criar($dto->paraEntidade());
    }
}
