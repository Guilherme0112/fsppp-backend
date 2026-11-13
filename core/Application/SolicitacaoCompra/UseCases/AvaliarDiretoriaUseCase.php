<?php

namespace Core\Application\SolicitacaoCompra\UseCases;

use Core\Application\SolicitacaoCompra\DTOs\AvaliarDiretoriaDTO;
use Core\Domain\SolicitacaoCompra\Entities\SolicitacaoCompra;
use Core\Domain\SolicitacaoCompra\Exceptions\SolicitacaoNaoEncontradaException;
use Core\Domain\SolicitacaoCompra\Exceptions\TransicaoStatusInvalidaException;
use Core\Domain\SolicitacaoCompra\Repositories\SolicitacaoCompraRepository;

class AvaliarDiretoriaUseCase
{
    public function __construct(private readonly SolicitacaoCompraRepository $repositorio) {}

    public function executar(AvaliarDiretoriaDTO $dto): SolicitacaoCompra
    {
        $solicitacao = $this->repositorio->buscarPorId($dto->id);

        if ($solicitacao === null) {
            throw new SolicitacaoNaoEncontradaException;
        }

        if (! in_array($solicitacao->status(), [
            SolicitacaoCompra::STATUS_PENDENTE,
            SolicitacaoCompra::STATUS_ENVIADO_DIRETORIA,
        ], true)) {
            throw new TransicaoStatusInvalidaException('Apenas solicitações aguardando a diretoria podem ser avaliadas.');
        }

        if (! $dto->aprovado && empty(trim($dto->motivoRejeicao ?? ''))) {
            throw new TransicaoStatusInvalidaException('O motivo da rejeição é obrigatório quando a solicitação não é aprovada.');
        }

        return $this->repositorio->avaliarDiretoria(
            $dto->id,
            $dto->aprovado,
            $dto->prioridade,
            $dto->motivoRejeicao,
            $dto->usuarioId
        );
    }
}
