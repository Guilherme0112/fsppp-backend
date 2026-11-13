<?php

namespace Core\Application\SolicitacaoCompra\UseCases;

use Core\Application\SolicitacaoCompra\DTOs\ConcluirSolicitacaoDTO;
use Core\Domain\SolicitacaoCompra\Entities\SolicitacaoCompra;
use Core\Domain\SolicitacaoCompra\Exceptions\SolicitacaoNaoEncontradaException;
use Core\Domain\SolicitacaoCompra\Exceptions\TransicaoStatusInvalidaException;
use Core\Domain\SolicitacaoCompra\Repositories\SolicitacaoCompraRepository;

class ConcluirSolicitacaoUseCase
{
    public function __construct(private readonly SolicitacaoCompraRepository $repositorio) {}

    public function executar(ConcluirSolicitacaoDTO $dto): SolicitacaoCompra
    {
        $solicitacao = $this->repositorio->buscarPorId($dto->id);

        if ($solicitacao === null) {
            throw new SolicitacaoNaoEncontradaException;
        }

        $statusConcluiveis = [
            SolicitacaoCompra::STATUS_APROVADO,
            SolicitacaoCompra::STATUS_APROVADO_DIRETORIA,
            SolicitacaoCompra::STATUS_RECURSOS_DISPONIVEIS_COMPRAS,
            SolicitacaoCompra::STATUS_RECURSOS_FUNDACAO,
        ];

        if (! in_array($solicitacao->status(), $statusConcluiveis, true)) {
            throw new TransicaoStatusInvalidaException('Somente solicitações aprovadas ou já encaminhadas para compras podem ser concluídas.');
        }

        return $this->repositorio->concluir($dto->id, $dto->usuarioId);
    }
}
