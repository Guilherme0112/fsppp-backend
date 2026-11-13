<?php

namespace Core\Application\SolicitacaoCompra\UseCases;

use Core\Application\SolicitacaoCompra\DTOs\ListarSolicitacoesUsuarioDTO;
use Core\Domain\SolicitacaoCompra\Repositories\SolicitacaoCompraRepository;

class ListarSolicitacoesUsuarioUseCase
{
    public function __construct(private readonly SolicitacaoCompraRepository $repositorio) {}

    public function executar(ListarSolicitacoesUsuarioDTO $dto): array
    {
        return $this->repositorio->listarPorUsuario(
            $dto->usuarioId,
            $dto->paraFiltros(),
            $dto->pagina,
            $dto->porPagina,
        );
    }
}
