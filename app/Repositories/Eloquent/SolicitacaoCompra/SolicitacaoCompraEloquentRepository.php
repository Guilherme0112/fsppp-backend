<?php

namespace App\Repositories\Eloquent\SolicitacaoCompra;

use App\Mapeadores\SolicitacaoCompra\SolicitacaoCompraMapeador;
use App\Models\SolicitacaoCompra\SolicitacaoCompra as SolicitacaoCompraModelo;
use Core\Domain\SolicitacaoCompra\Entities\SolicitacaoCompra;
use Core\Domain\SolicitacaoCompra\Repositories\SolicitacaoCompraRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class SolicitacaoCompraEloquentRepository implements SolicitacaoCompraRepository
{
    private const RELACOES = [
        'usuario',
        'aprovadoCanceladoPorUsuario',
        'concluidoPorUsuario',
        'diretoriaPorUsuario',
        'projetosPorUsuario',
        'comprasPorUsuario',
    ];

    public function __construct(private readonly SolicitacaoCompraMapeador $mapeador) {}

    public function buscarPorId(int $id): ?SolicitacaoCompra
    {
        $modelo = SolicitacaoCompraModelo::query()->with(self::RELACOES)->find($id);

        return $modelo ? $this->mapeador->paraEntidade($modelo) : null;
    }

    public function buscarPorIdEUsuario(int $id, int $usuarioId): ?SolicitacaoCompra
    {
        $modelo = SolicitacaoCompraModelo::query()
            ->with(self::RELACOES)
            ->where('id', $id)
            ->where('usuario_id', $usuarioId)
            ->first();

        return $modelo ? $this->mapeador->paraEntidade($modelo) : null;
    }

    public function criar(SolicitacaoCompra $solicitacao): SolicitacaoCompra
    {
        return \DB::transaction(function () use ($solicitacao) {
            $modelo = $this->mapeador->paraModelo($solicitacao);
            $modelo->save();

            return $this->mapeador->paraEntidade($modelo->refresh()->load(self::RELACOES));
        });
    }

    public function atualizar(int $id, SolicitacaoCompra $solicitacao): SolicitacaoCompra
    {
        return \DB::transaction(function () use ($id, $solicitacao) {
            $modelo = SolicitacaoCompraModelo::query()->findOrFail($id);
            $this->mapeador->paraModelo($solicitacao, $modelo)->update();

            return $this->mapeador->paraEntidade($modelo->refresh()->load(self::RELACOES));
        });
    }

    public function enviarParaDiretoria(int $id, int $usuarioId): SolicitacaoCompra
    {
        return \DB::transaction(function () use ($id) {
            $modelo = SolicitacaoCompraModelo::query()->findOrFail($id);
            $modelo->update([
                'status' => SolicitacaoCompraModelo::STATUS_ENVIADO_DIRETORIA,
            ]);

            return $this->mapeador->paraEntidade($modelo->refresh()->load(self::RELACOES));
        });
    }

    public function avaliarDiretoria(int $id, bool $aprovado, string $prioridade, ?string $motivoRejeicao, int $usuarioId): SolicitacaoCompra
    {
        return \DB::transaction(function () use ($id, $aprovado, $prioridade, $motivoRejeicao, $usuarioId) {
            $modelo = SolicitacaoCompraModelo::query()->findOrFail($id);

            $status = $aprovado
                ? SolicitacaoCompraModelo::STATUS_ANALISE_PROJETOS
                : SolicitacaoCompraModelo::STATUS_REJEITADO_DIRETORIA;

            $modelo->update([
                'status' => $status,
                'prioridade' => $prioridade,
                'motivo_rejeicao' => $aprovado ? null : $motivoRejeicao,
                'diretoria_por' => $usuarioId,
            ]);

            return $this->mapeador->paraEntidade($modelo->refresh()->load(self::RELACOES));
        });
    }

    public function avaliarProjetos(int $id, bool $recursosExistem, int $usuarioId): SolicitacaoCompra
    {
        return \DB::transaction(function () use ($id, $recursosExistem, $usuarioId) {
            $modelo = SolicitacaoCompraModelo::query()->findOrFail($id);

            $status = $recursosExistem
                ? SolicitacaoCompraModelo::STATUS_RECURSOS_DISPONIVEIS_COMPRAS
                : SolicitacaoCompraModelo::STATUS_RECURSOS_FUNDACAO;

            $modelo->update([
                'status' => $status,
                'recursos_existem' => $recursosExistem,
                'projetos_por' => $usuarioId,
            ]);

            return $this->mapeador->paraEntidade($modelo->refresh()->load(self::RELACOES));
        });
    }

    public function atualizarStatusCompra(int $id, string $statusCompra, int $usuarioId): SolicitacaoCompra
    {
        return \DB::transaction(function () use ($id, $statusCompra, $usuarioId) {
            $modelo = SolicitacaoCompraModelo::query()->findOrFail($id);
            $modelo->update([
                'status_compra' => $statusCompra,
                'compras_por' => $usuarioId,
            ]);

            return $this->mapeador->paraEntidade($modelo->refresh()->load(self::RELACOES));
        });
    }

    public function aprovarOuCancelar(int $id, string $status, int $usuarioId): SolicitacaoCompra
    {
        return \DB::transaction(function () use ($id, $status, $usuarioId) {
            $modelo = SolicitacaoCompraModelo::query()->findOrFail($id);
            $modelo->update([
                'status' => $status,
                'aprovado_cancelado_por' => $usuarioId,
            ]);

            return $this->mapeador->paraEntidade($modelo->refresh()->load(self::RELACOES));
        });
    }

    public function concluir(int $id, int $usuarioId): SolicitacaoCompra
    {
        return \DB::transaction(function () use ($id, $usuarioId) {
            $modelo = SolicitacaoCompraModelo::query()->findOrFail($id);
            $modelo->update([
                'status' => SolicitacaoCompraModelo::STATUS_CONCLUIDO,
                'concluido_por' => $usuarioId,
            ]);

            return $this->mapeador->paraEntidade($modelo->refresh()->load(self::RELACOES));
        });
    }

    public function listarPorUsuario(int $usuarioId, array $filtros, int $pagina, int $porPagina): array
    {
        $paginador = $this->aplicarFiltros(
            SolicitacaoCompraModelo::query()->with(self::RELACOES)->where('usuario_id', $usuarioId),
            $filtros,
        )
            ->latest()
            ->paginate($porPagina, ['*'], 'page', $pagina);

        return $this->paraArrayPaginado($paginador);
    }

    public function listarTodas(array $filtros, int $pagina, int $porPagina): array
    {
        $paginador = $this->aplicarFiltros(
            SolicitacaoCompraModelo::query()->with(self::RELACOES),
            $filtros,
        )
            ->latest()
            ->paginate($porPagina, ['*'], 'page', $pagina);

        return $this->paraArrayPaginado($paginador);
    }

    public function resumo(?int $usuarioId = null): array
    {
        $consulta = SolicitacaoCompraModelo::query()
            ->when($usuarioId !== null, fn ($query) => $query->where('usuario_id', $usuarioId));

        $contagens = $consulta
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->map(fn ($total) => (int) $total)
            ->all();

        return [
            'total' => array_sum($contagens),
            'por_status' => $contagens,
        ];
    }

    private function aplicarFiltros($consulta, array $filtros)
    {
        return $consulta
            ->when($filtros['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filtros['usuario_id'] ?? null, fn ($query, $usuarioId) => $query->where('usuario_id', $usuarioId))
            ->when($filtros['busca'] ?? null, function ($query, $busca) {
                $termo = '%'.mb_strtolower(trim((string) $busca)).'%';

                $query->whereRaw('LOWER(titulo) LIKE ?', [$termo]);
            });
    }

    private function paraArrayPaginado(LengthAwarePaginator $paginador): array
    {
        return [
            'data' => $paginador->getCollection()
                ->map(fn (SolicitacaoCompraModelo $modelo): SolicitacaoCompra => $this->mapeador->paraEntidade($modelo))
                ->values()
                ->all(),
            'current_page' => $paginador->currentPage(),
            'last_page' => $paginador->lastPage(),
            'total' => $paginador->total(),
        ];
    }
}
