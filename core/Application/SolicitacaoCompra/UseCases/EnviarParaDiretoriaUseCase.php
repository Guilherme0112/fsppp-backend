<?php

namespace Core\Application\SolicitacaoCompra\UseCases;

use Core\Application\SolicitacaoCompra\DTOs\EnviarParaDiretoriaDTO;
use Core\Domain\SolicitacaoCompra\Entities\SolicitacaoCompra;
use Core\Domain\SolicitacaoCompra\Exceptions\SolicitacaoNaoEncontradaException;
use Core\Domain\SolicitacaoCompra\Exceptions\TransicaoStatusInvalidaException;
use Core\Domain\SolicitacaoCompra\Repositories\SolicitacaoCompraRepository;

class EnviarParaDiretoriaUseCase
{
    public function __construct(private readonly SolicitacaoCompraRepository $repositorio) {}

    public function executar(EnviarParaDiretoriaDTO $dto): SolicitacaoCompra
    {
        $solicitacao = $this->repositorio->buscarPorId($dto->id);

        if ($solicitacao === null) {
            throw new SolicitacaoNaoEncontradaException;
        }

        if ($solicitacao->usuarioId() !== $dto->usuarioId) {
            throw new TransicaoStatusInvalidaException('Você não tem permissão para enviar esta solicitação.');
        }

        if ($solicitacao->status() !== SolicitacaoCompra::STATUS_PENDENTE) {
            throw new TransicaoStatusInvalidaException('Apenas solicitações pendentes podem ser enviadas para a diretoria.');
        }

        return $this->repositorio->enviarParaDiretoria($dto->id, $dto->usuarioId);
    }
}
