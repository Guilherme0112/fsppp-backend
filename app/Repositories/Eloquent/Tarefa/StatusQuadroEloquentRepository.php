<?php

namespace App\Repositories\Eloquent\Tarefa;

use App\Mapeadores\Tarefa\StatusQuadroMapeador;
use App\Models\Tarefa\StatusQuadro as StatusQuadroModelo;
use Core\Domain\Tarefa\Entities\StatusQuadro;
use Core\Domain\Tarefa\Repositories\StatusQuadroRepository;

class StatusQuadroEloquentRepository implements StatusQuadroRepository
{
    public function __construct(private readonly StatusQuadroMapeador $mapeador) {}

    public function listarPorQuadroId(int $quadroId): array
    {
        return StatusQuadroModelo::query()
            ->where('quadro_id', $quadroId)
            ->paginate(20)
            ->through(fn (StatusQuadroModelo $modelo): StatusQuadro => $this->mapeador->paraEntidade($modelo))
            ->toArray();
    }

    public function criar(StatusQuadro $statusQuadro): StatusQuadro
    {
        return \DB::transaction(function () use ($statusQuadro) {
            $modelo = $this->mapeador->paraModelo($statusQuadro);
            $modelo->save();

            return $this->mapeador->paraEntidade($modelo->refresh());
        });
    }

    public function atualizar(int $id, StatusQuadro $statusQuadro): StatusQuadro
    {
        return \DB::transaction(function () use ($id, $statusQuadro) {
            $modelo = StatusQuadroModelo::query()->findOrFail($id);
            $this->mapeador->paraModelo($statusQuadro, $modelo)->update();

            return $this->mapeador->paraEntidade($modelo->refresh());
        });
    }

    public function apagar(int $id): void
    {
        \DB::transaction(function () use ($id) {
            StatusQuadroModelo::query()->findOrFail($id)->delete();
        });
    }

    public function pertenceAoUsuario(int $id, int $usuarioId): bool
    {
        return StatusQuadroModelo::query()->whereKey($id)
            ->whereHas('quadro', fn ($consulta) => $consulta->where('usuario_id', $usuarioId))
            ->exists();
    }

    public function pertenceAoQuadro(int $id, int $quadroId): bool
    {
        return StatusQuadroModelo::query()->whereKey($id)->where('quadro_id', $quadroId)->exists();
    }
}
