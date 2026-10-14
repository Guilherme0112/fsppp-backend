<?php

namespace App\Repositories\Eloquent\Usuario;

use App\Mapeadores\UsuarioMapeador;
use App\Models\Usuario as UsuarioModelo;
use Core\Domain\Usuario\Entities\Usuario;
use Core\Domain\Usuario\Repositories\UsuarioRepository;
use Illuminate\Support\Facades\DB;

class UsuarioEloquentRepository implements UsuarioRepository
{
    public function __construct(private readonly UsuarioMapeador $mapeador) {}

    public function listar(int $pagina = 1, int $porPagina = 20): array
    {
        return UsuarioModelo::query()
            ->with('roles')
            ->paginate($porPagina, ['*'], 'page', $pagina)
            ->through(fn (UsuarioModelo $modelo): Usuario => $this->mapeador->paraEntidade($modelo))
            ->toArray();
    }

    public function criar(Usuario $usuario): Usuario
    {
        return DB::transaction(function () use ($usuario) {
            $modelo = $this->mapeador->paraModelo($usuario);
            $modelo->save();
            $modelo->syncRoles($usuario->papeis());

            return $this->mapeador->paraEntidade($modelo->refresh());
        });
    }

    public function atualizar(int $id, Usuario $usuario): Usuario
    {
        return DB::transaction(function () use ($id, $usuario) {
            $modelo = UsuarioModelo::query()->findOrFail($id);
            $this->mapeador->paraModelo($usuario, $modelo)->update();
            $modelo->syncRoles($usuario->papeis());

            return $this->mapeador->paraEntidade($modelo->refresh());
        });
    }

    public function apagar(int $id): void
    {
        DB::transaction(function () use ($id) {
            UsuarioModelo::query()->findOrFail($id)->delete();
        });
    }
}
