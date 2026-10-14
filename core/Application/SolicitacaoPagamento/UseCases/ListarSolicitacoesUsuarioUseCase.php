<?php

namespace Core\Application\SolicitacaoPagamento\UseCases;

use Core\Application\SolicitacaoPagamento\DTOs\ListarSolicitacoesUsuarioDTO;
use Core\Domain\SolicitacaoPagamento\Repositories\SolicitacaoPagamentoRepository;

class ListarSolicitacoesUsuarioUseCase
{
    public function __construct(private readonly SolicitacaoPagamentoRepository $repositorio) {}

    public function executar(ListarSolicitacoesUsuarioDTO $dto): array
    {
        return $this->repositorio->listarPorUsuario($dto->usuarioId, $dto->pagina, $dto->porPagina);
    }
}
