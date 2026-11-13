<?php

namespace Core\Domain\SolicitacaoCompra\Repositories;

use Core\Domain\SolicitacaoCompra\Entities\SolicitacaoCompra;

interface SolicitacaoCompraRepository
{
    public function buscarPorId(int $id): ?SolicitacaoCompra;

    public function buscarPorIdEUsuario(int $id, int $usuarioId): ?SolicitacaoCompra;

    public function criar(SolicitacaoCompra $solicitacao): SolicitacaoCompra;

    public function atualizar(int $id, SolicitacaoCompra $solicitacao): SolicitacaoCompra;

    public function enviarParaDiretoria(int $id, int $usuarioId): SolicitacaoCompra;

    public function avaliarDiretoria(int $id, bool $aprovado, string $prioridade, ?string $motivoRejeicao, int $usuarioId): SolicitacaoCompra;

    public function avaliarProjetos(int $id, bool $recursosExistem, int $usuarioId): SolicitacaoCompra;

    public function atualizarStatusCompra(int $id, string $statusCompra, int $usuarioId): SolicitacaoCompra;

    public function aprovarOuCancelar(int $id, string $status, int $usuarioId): SolicitacaoCompra;

    public function concluir(int $id, int $usuarioId): SolicitacaoCompra;

    public function listarPorUsuario(int $usuarioId, array $filtros, int $pagina, int $porPagina): array;

    public function listarTodas(array $filtros, int $pagina, int $porPagina): array;

    public function resumo(?int $usuarioId = null): array;
}
