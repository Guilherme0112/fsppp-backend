<?php

namespace Core\Application\SolicitacaoCompra\UseCases;

use Core\Domain\SolicitacaoCompra\Repositories\SolicitacaoCompraRepository;

class ObterResumoSolicitacoesUseCase
{
    public function __construct(private readonly SolicitacaoCompraRepository $repositorio) {}

    /**
     * @return array{total: int, por_status: array<string, int>}
     */
    public function executar(?int $usuarioId = null): array
    {
        return $this->repositorio->resumo($usuarioId);
    }
}
