<?php

namespace Core\Application\SolicitacaoPagamento\UseCases;

use Core\Application\SolicitacaoPagamento\DTOs\AtualizarSolicitacaoPagamentoDTO;
use Core\Domain\SolicitacaoPagamento\Entities\SolicitacaoPagamento;
use Core\Domain\SolicitacaoPagamento\Exceptions\SolicitacaoNaoEditavelException;
use Core\Domain\SolicitacaoPagamento\Exceptions\SolicitacaoNaoEncontradaException;
use Core\Domain\SolicitacaoPagamento\Repositories\SolicitacaoPagamentoRepository;

class AtualizarSolicitacaoPagamentoUseCase
{
    public function __construct(private readonly SolicitacaoPagamentoRepository $repositorio) {}

    public function executar(AtualizarSolicitacaoPagamentoDTO $dto): SolicitacaoPagamento
    {
        $solicitacao = $this->repositorio->buscarPorIdEUsuario($dto->id, $dto->usuarioId);

        if ($solicitacao === null) {
            throw new SolicitacaoNaoEncontradaException;
        }

        if ($solicitacao->status() !== SolicitacaoPagamento::STATUS_PENDENTE) {
            throw new SolicitacaoNaoEditavelException;
        }

        return $this->repositorio->atualizar($dto->id, $dto->paraEntidade());
    }
}
