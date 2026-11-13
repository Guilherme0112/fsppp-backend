<?php

namespace Core\Application\SolicitacaoCompra\UseCases;

use Core\Application\SolicitacaoCompra\DTOs\AtualizarSolicitacaoCompraDTO;
use Core\Domain\SolicitacaoCompra\Entities\SolicitacaoCompra;
use Core\Domain\SolicitacaoCompra\Exceptions\SolicitacaoNaoEditavelException;
use Core\Domain\SolicitacaoCompra\Exceptions\SolicitacaoNaoEncontradaException;
use Core\Domain\SolicitacaoCompra\Repositories\SolicitacaoCompraRepository;

class AtualizarSolicitacaoCompraUseCase
{
    public function __construct(private readonly SolicitacaoCompraRepository $repositorio) {}

    public function executar(AtualizarSolicitacaoCompraDTO $dto): SolicitacaoCompra
    {
        $solicitacao = $this->repositorio->buscarPorIdEUsuario($dto->id, $dto->usuarioId);

        if ($solicitacao === null) {
            throw new SolicitacaoNaoEncontradaException;
        }

        if ($solicitacao->status() !== SolicitacaoCompra::STATUS_PENDENTE) {
            throw new SolicitacaoNaoEditavelException;
        }

        return $this->repositorio->atualizar($dto->id, $dto->paraEntidade());
    }
}
