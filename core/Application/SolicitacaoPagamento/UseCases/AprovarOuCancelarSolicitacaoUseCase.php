<?php

namespace Core\Application\SolicitacaoPagamento\UseCases;

use Core\Application\SolicitacaoPagamento\DTOs\AprovarOuCancelarSolicitacaoDTO;
use Core\Domain\SolicitacaoPagamento\Entities\SolicitacaoPagamento;
use Core\Domain\SolicitacaoPagamento\Exceptions\SolicitacaoNaoEncontradaException;
use Core\Domain\SolicitacaoPagamento\Exceptions\TransicaoStatusInvalidaException;
use Core\Domain\SolicitacaoPagamento\Repositories\SolicitacaoPagamentoRepository;

class AprovarOuCancelarSolicitacaoUseCase
{
    public function __construct(private readonly SolicitacaoPagamentoRepository $repositorio) {}

    public function executar(AprovarOuCancelarSolicitacaoDTO $dto): SolicitacaoPagamento
    {
        $solicitacao = $this->repositorio->buscarPorId($dto->id);

        if ($solicitacao === null) {
            throw new SolicitacaoNaoEncontradaException;
        }

        if ($solicitacao->status() !== SolicitacaoPagamento::STATUS_PENDENTE) {
            throw new TransicaoStatusInvalidaException('Somente solicitações pendentes podem ser aprovadas ou canceladas.');
        }

        return $this->repositorio->aprovarOuCancelar($dto->id, $dto->status, $dto->usuarioId);
    }
}
