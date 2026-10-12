<?php

namespace App\Repositories\Eloquent\Tarefa;

use App\Mapeadores\Tarefa\TarefaMapeador;
use App\Models\Tarefa\Tarefa as TarefaModelo;
use Core\Domain\Tarefa\Entities\Tarefa;
use Core\Domain\Tarefa\Repositories\TarefaRepository;

class TarefaEloquentRepository implements TarefaRepository
{
    public function __construct(private readonly TarefaMapeador $mapeador) {}

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
