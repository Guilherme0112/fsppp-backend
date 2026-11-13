<?php

namespace Core\Application\SolicitacaoCompra\UseCases;

use Core\Application\SolicitacaoCompra\DTOs\AvaliarProjetosDTO;
use Core\Domain\SolicitacaoCompra\Entities\SolicitacaoCompra;
use Core\Domain\SolicitacaoCompra\Exceptions\SolicitacaoNaoEncontradaException;
use Core\Domain\SolicitacaoCompra\Exceptions\TransicaoStatusInvalidaException;
use Core\Domain\SolicitacaoCompra\Repositories\SolicitacaoCompraRepository;

class AvaliarProjetosUseCase
{
    public function __construct(private readonly SolicitacaoCompraRepository $repositorio) {}

    public function executar(AvaliarProjetosDTO $dto): SolicitacaoCompra
    {
        $solicitacao = $this->repositorio->buscarPorId($dto->id);

        if ($solicitacao === null) {
            throw new SolicitacaoNaoEncontradaException;
        }

        if ($solicitacao->status() !== SolicitacaoCompra::STATUS_ANALISE_PROJETOS) {
            throw new TransicaoStatusInvalidaException('Apenas solicitações em análise pelo setor de projetos podem ser avaliadas.');
        }

        return $this->repositorio->avaliarProjetos($dto->id, $dto->recursosExistem, $dto->usuarioId);
    }
}
