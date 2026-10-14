<?php

namespace Core\Domain\SolicitacaoPagamento\Repositories;

use Core\Domain\SolicitacaoPagamento\Entities\SolicitacaoPagamento;

interface SolicitacaoPagamentoRepository
{
    public function buscarPorId(int $id): ?SolicitacaoPagamento;

    public function buscarPorIdEUsuario(int $id, int $usuarioId): ?SolicitacaoPagamento;

    public function criar(SolicitacaoPagamento $solicitacao): SolicitacaoPagamento;

    public function atualizar(int $id, SolicitacaoPagamento $solicitacao): SolicitacaoPagamento;

    public function aprovarOuCancelar(int $id, string $status, int $usuarioId): SolicitacaoPagamento;

    public function concluir(int $id, int $usuarioId): SolicitacaoPagamento;

    public function listarPorUsuario(int $usuarioId, int $pagina, int $porPagina): array;

    public function listarTodas(array $filtros, int $pagina, int $porPagina): array;
}
