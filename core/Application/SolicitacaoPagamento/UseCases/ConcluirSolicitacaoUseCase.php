<?php

namespace Core\Application\SolicitacaoPagamento\UseCases;

use Core\Application\SolicitacaoPagamento\DTOs\ConcluirSolicitacaoDTO;
use Core\Domain\SolicitacaoPagamento\Entities\SolicitacaoPagamento;
use Core\Domain\SolicitacaoPagamento\Exceptions\SolicitacaoNaoEncontradaException;
use Core\Domain\SolicitacaoPagamento\Exceptions\TransicaoStatusInvalidaException;
use Core\Domain\SolicitacaoPagamento\Repositories\SolicitacaoPagamentoRepository;

class ConcluirSolicitacaoUseCase
{
    public function __construct(private readonly SolicitacaoPagamentoRepository $repositorio) {}

    public function executar(ConcluirSolicitacaoDTO $dto): SolicitacaoPagamento
    {
        $solicitacao = $this->repositorio->buscarPorId($dto->id);

        if ($solicitacao === null) {
            throw new SolicitacaoNaoEncontradaException;
        }

        if ($solicitacao->status() !== SolicitacaoPagamento::STATUS_APROVADO) {
            throw new TransicaoStatusInvalidaException('Somente solicitações aprovadas podem ser concluídas.');
        }

        return $this->repositorio->concluir($dto->id, $dto->usuarioId);
    }
}
