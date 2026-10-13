<?php

namespace App\Repositories\Eloquent\Tarefa;

use App\Mapeadores\Tarefa\QuadroMapeador;
use App\Models\Tarefa\Quadro as QuadroModelo;
use Core\Domain\Tarefa\Entities\Quadro;
use Core\Domain\Tarefa\Repositories\QuadroRepository;
use Illuminate\Support\Facades\DB;

class QuadroEloquentRepository implements QuadroRepository
{
    public function __construct(private readonly QuadroMapeador $mapeador) {}

    public function listarPorUsuarioId(int $usuarioId): array
    {
        return QuadroModelo::query()
            ->where('usuario_id', $usuarioId)
            ->paginate(20)
            ->through(fn (QuadroModelo $modelo): Quadro => $this->mapeador->paraEntidade($modelo))
            ->toArray();
    }

    public function criar(Quadro $quadro): Quadro
    {
        return DB::transaction(function () use ($quadro) {
            $modelo = $this->mapeador->paraModelo($quadro);
            $modelo->save();

            return $this->mapeador->paraEntidade($modelo->refresh());
        });
    }

    public function atualizar(int $id, Quadro $quadro): Quadro
    {
        return DB::transaction(function () use ($id, $quadro) {
            $modelo = QuadroModelo::query()->findOrFail($id);
            $this->mapeador->paraModelo($quadro, $modelo)->update();

            return $this->mapeador->paraEntidade($modelo->refresh());
        });
    }

    public function apagar(int $id): void
    {
        DB::transaction(function () use ($id) {
            QuadroModelo::query()->findOrFail($id)->delete();
        });
    }

    public function pertenceAoUsuario(int $id, int $usuarioId): bool
    {
        return QuadroModelo::query()->whereKey($id)->where('usuario_id', $usuarioId)->exists();
    }

    public function convidarUmUsuario(int $id, int $usuarioId): void
    {
        DB::transaction(function () use ($id, $usuarioId) {
            $quadro = QuadroModelo::query()->findOrFail($id);
            $quadro->usuarios()->attach($usuarioId);
        });
    }
}
