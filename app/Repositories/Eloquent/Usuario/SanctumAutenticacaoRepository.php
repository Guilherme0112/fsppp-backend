<?php

namespace App\Repositories\Eloquent\Usuario;

use App\Mapeadores\UsuarioMapeador;
use App\Models\Usuario;
use Core\Domain\Usuario\Entities\SessaoAutenticada;
use Core\Domain\Usuario\Repositories\AutenticacaoRepository;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class SanctumAutenticacaoRepository implements AutenticacaoRepository
{
    public function __construct(private readonly UsuarioMapeador $mapeador) {}

    public function autenticar(string $email, string $senha, string $nomeDispositivo): ?SessaoAutenticada
    {
        $usuario = Usuario::query()->where('email', $email)->first();

        if ($usuario === null || ! Hash::check($senha, $usuario->senha)) {
            return null;
        }

        return new SessaoAutenticada(
            usuario: $this->mapeador->paraEntidade($usuario),
            token: $usuario->createToken($nomeDispositivo)->plainTextToken,
            permissoes: $usuario->getAllPermissions()->pluck('name')->all(),
        );
    }

    public function revogarToken(int $tokenId): void
    {
        PersonalAccessToken::query()->whereKey($tokenId)->delete();
    }
}
