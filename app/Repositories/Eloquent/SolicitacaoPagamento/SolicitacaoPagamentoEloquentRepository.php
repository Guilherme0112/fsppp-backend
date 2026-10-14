<?php

namespace App\Repositories\Eloquent\SolicitacaoPagamento;

use App\Mapeadores\SolicitacaoPagamento\SolicitacaoPagamentoMapeador;
use App\Models\SolicitacaoPagamento\SolicitacaoPagamento as SolicitacaoPagamentoModelo;
use Core\Domain\SolicitacaoPagamento\Entities\SolicitacaoPagamento;
use Core\Domain\SolicitacaoPagamento\Repositories\SolicitacaoPagamentoRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class SolicitacaoPagamentoEloquentRepository implements SolicitacaoPagamentoRepository
{
    public function __construct(private readonly SolicitacaoPagamentoMapeador $mapeador) {}

    public function buscarPorId(int $id): ?SolicitacaoPagamento
    {
        $modelo = SolicitacaoPagamentoModelo::find($id);

        return $modelo ? $this->mapeador->paraEntidade($modelo) : null;
    }

    public function buscarPorIdEUsuario(int $id, int $usuarioId): ?SolicitacaoPagamento
    {
        $modelo = SolicitacaoPagamentoModelo::query()
            ->where('id', $id)
            ->where('usuario_id', $usuarioId)
            ->first();

        return $modelo ? $this->mapeador->paraEntidade($modelo) : null;
    }

    public function criar(SolicitacaoPagamento $solicitacao): SolicitacaoPagamento
    {
        return \DB::transaction(function () use ($solicitacao) {
            $modelo = $this->mapeador->paraModelo($solicitacao);
            $modelo->save();

            return $this->mapeador->paraEntidade($modelo->refresh());
        });
    }

    public function atualizar(int $id, SolicitacaoPagamento $solicitacao): SolicitacaoPagamento
    {
        return \DB::transaction(function () use ($id, $solicitacao) {
            $modelo = SolicitacaoPagamentoModelo::query()->findOrFail($id);
            $this->mapeador->paraModelo($solicitacao, $modelo)->update();

            return $this->mapeador->paraEntidade($modelo->refresh());
        });
    }

    public function aprovarOuCancelar(int $id, string $status, int $usuarioId): SolicitacaoPagamento
    {
        return \DB::transaction(function () use ($id, $status, $usuarioId) {
            $modelo = SolicitacaoPagamentoModelo::query()->findOrFail($id);
            $modelo->update([
                'status' => $status,
                'aprovado_cancelado_por' => $usuarioId,
            ]);

            return $this->mapeador->paraEntidade($modelo->refresh());
        });
    }

    public function concluir(int $id, int $usuarioId): SolicitacaoPagamento
    {
        return \DB::transaction(function () use ($id, $usuarioId) {
            $modelo = SolicitacaoPagamentoModelo::query()->findOrFail($id);
            $modelo->update([
                'status' => SolicitacaoPagamentoModelo::STATUS_CONCLUIDO,
                'concluido_por' => $usuarioId,
            ]);

            return $this->mapeador->paraEntidade($modelo->refresh());
        });
    }

    public function listarPorUsuario(int $usuarioId, int $pagina, int $porPagina): array
    {
        $paginador = SolicitacaoPagamentoModelo::query()
            ->where('usuario_id', $usuarioId)
            ->latest()
            ->paginate($porPagina, ['*'], 'page', $pagina);

        return $this->paraArrayPaginado($paginador);
    }

    public function listarTodas(array $filtros, int $pagina, int $porPagina): array
    {
        $paginador = SolicitacaoPagamentoModelo::query()
            ->with(['usuario', 'aprovadoCanceladoPorUsuario', 'concluidoPorUsuario'])
            ->when($filtros['status'] ?? null, fn ($consulta, $status) => $consulta->where('status', $status))
            ->when($filtros['usuario_id'] ?? null, fn ($consulta, $usuarioId) => $consulta->where('usuario_id', $usuarioId))
            ->latest()
            ->paginate($porPagina, ['*'], 'page', $pagina);

        return $this->paraArrayPaginado($paginador);
    }

    private function paraArrayPaginado(LengthAwarePaginator $paginador): array
    {
        return [
            'data' => $paginador->getCollection()
                ->map(fn (SolicitacaoPagamentoModelo $modelo): SolicitacaoPagamento => $this->mapeador->paraEntidade($modelo))
                ->values()
                ->all(),
            'current_page' => $paginador->currentPage(),
            'last_page' => $paginador->lastPage(),
            'total' => $paginador->total(),
        ];
    }
}
