<?php

namespace Core\Application\SolicitacaoCompra\UseCases;

use Core\Application\SolicitacaoCompra\DTOs\AtualizarStatusCompraDTO;
use Core\Domain\SolicitacaoCompra\Entities\SolicitacaoCompra;
use Core\Domain\SolicitacaoCompra\Exceptions\SolicitacaoNaoEncontradaException;
use Core\Domain\SolicitacaoCompra\Exceptions\TransicaoStatusInvalidaException;
use Core\Domain\SolicitacaoCompra\Repositories\SolicitacaoCompraRepository;

class AtualizarStatusCompraUseCase
{
    public function __construct(private readonly SolicitacaoCompraRepository $repositorio) {}

    public function executar(AtualizarStatusCompraDTO $dto): SolicitacaoCompra
    {
        $solicitacao = $this->repositorio->buscarPorId($dto->id);

        if ($solicitacao === null) {
            throw new SolicitacaoNaoEncontradaException;
        }

        if (!in_array($solicitacao->status(), [SolicitacaoCompra::STATUS_RECURSOS_DISPONIVEIS_COMPRAS, SolicitacaoCompra::STATUS_RECURSOS_FUNDACAO])) {
            throw new TransicaoStatusInvalidaException('Apenas solicitações encaminhadas para compras ou fundação podem ter o status de compra atualizado.');
        }

        return $this->repositorio->atualizarStatusCompra($dto->id, $dto->statusCompra, $dto->usuarioId);
    }
}
