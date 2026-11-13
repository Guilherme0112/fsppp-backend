<?php

namespace Core\Application\SolicitacaoCompra\UseCases;

use Core\Application\SolicitacaoCompra\DTOs\AprovarOuCancelarSolicitacaoDTO;
use Core\Domain\SolicitacaoCompra\Entities\SolicitacaoCompra;
use Core\Domain\SolicitacaoCompra\Exceptions\SolicitacaoNaoEncontradaException;
use Core\Domain\SolicitacaoCompra\Exceptions\TransicaoStatusInvalidaException;
use Core\Domain\SolicitacaoCompra\Repositories\SolicitacaoCompraRepository;

class AprovarOuCancelarSolicitacaoUseCase
{
    public function __construct(private readonly SolicitacaoCompraRepository $repositorio) {}

    public function executar(AprovarOuCancelarSolicitacaoDTO $dto): SolicitacaoCompra
    {
        $solicitacao = $this->repositorio->buscarPorId($dto->id);

        if ($solicitacao === null) {
            throw new SolicitacaoNaoEncontradaException;
        }

        if ($solicitacao->status() !== SolicitacaoCompra::STATUS_PENDENTE) {
            throw new TransicaoStatusInvalidaException('Somente solicitações pendentes podem ser aprovadas ou canceladas.');
        }

        return $this->repositorio->aprovarOuCancelar($dto->id, $dto->status, $dto->usuarioId);
    }
}
