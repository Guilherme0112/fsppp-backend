<?php

namespace App\Repositories\Eloquent\Tarefa;

use App\Mapeadores\Tarefa\StatusQuadroMapeador;
use App\Mapeadores\Tarefa\TarefaMapeador;
use App\Models\Tarefa\StatusQuadro as StatusQuadroModelo;
use App\Models\Tarefa\Tarefa as TarefaModelo;
use Core\Domain\Tarefa\Entities\Tarefa;
use Core\Domain\Tarefa\Repositories\TarefaRepository;

class TarefaEloquentRepository implements TarefaRepository
{
    public function __construct(
        private readonly TarefaMapeador $mapeador,
        private readonly StatusQuadroMapeador $statusQuadroMapeador,
    ) {}

    public function listarPorQuadroId(int $quadroId): array
    {
        return TarefaModelo::query()
            ->where('quadro_id', $quadroId)
            ->paginate(20)
            ->through(fn (TarefaModelo $modelo): Tarefa => $this->mapeador->paraEntidade($modelo))
            ->toArray();
    }

    public function listarPorUsuarioId(int $usuarioId): array
    {
        return TarefaModelo::query()
            ->whereHas('quadro', fn ($consulta) => $consulta->where('usuario_id', $usuarioId))
            ->paginate(20)
            ->through(fn (TarefaModelo $modelo): Tarefa => $this->mapeador->paraEntidade($modelo))
            ->toArray();
    }

    public function listarAgrupadoPorStatusDoQuadro(int $quadroId, int $limitePorStatus = 10): array
    {
        return StatusQuadroModelo::query()
            ->where('quadro_id', $quadroId)
            ->get()
            ->map(fn (StatusQuadroModelo $status) => [
                'status' => $this->statusQuadroMapeador->paraEntidade($status),
                'tarefas' => $status->tarefas()
                    ->orderByDesc('criado_em')
                    ->paginate($limitePorStatus, ['*'], 'page', 1)
                    ->through(fn (TarefaModelo $modelo): Tarefa => $this->mapeador->paraEntidade($modelo))
                    ->toArray(),
            ])
            ->values()
            ->all();
    }

    public function listarPorStatus(int $statusId, int $pagina, int $porPagina = 10): array
    {
        $paginador = TarefaModelo::query()
            ->where('status_id', $statusId)
            ->orderByDesc('criado_em')
            ->paginate($porPagina, ['*'], 'page', $pagina);

        return [
            'data' => $paginador->getCollection()
                ->map(fn (TarefaModelo $modelo): Tarefa => $this->mapeador->paraEntidade($modelo))
                ->values()
                ->all(),
            'current_page' => $paginador->currentPage(),
            'last_page' => $paginador->lastPage(),
            'total' => $paginador->total(),
        ];
    }

    public function criar(int $quadroId, Tarefa $tarefa): Tarefa
    {
        return \DB::transaction(function () use ($quadroId, $tarefa) {
            $modelo = $this->mapeador->paraModelo($tarefa);
            $modelo->quadro_id = $quadroId;
            $modelo->save();

            return $this->mapeador->paraEntidade($modelo->refresh());
        });
    }

    public function atualizar(int $id, Tarefa $tarefa): Tarefa
    {
        return \DB::transaction(function () use ($id, $tarefa) {
            $modelo = TarefaModelo::query()->findOrFail($id);
            $this->mapeador->paraModelo($tarefa, $modelo)->update();
            return $this->mapeador->paraEntidade($modelo->refresh());
        });
    }

    public function apagar(int $id): void
    {
        \DB::transaction(function () use ($id) {
            TarefaModelo::query()->findOrFail($id)->delete();
        });
    }

    public function pertenceAoUsuario(int $id, int $usuarioId): bool
    {
        return TarefaModelo::query()->whereKey($id)
            ->whereHas('quadro', fn ($consulta) => $consulta->where('usuario_id', $usuarioId))
            ->exists();
    }
}
