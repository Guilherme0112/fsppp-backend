<?php

namespace App\Mapeadores;

use App\Models\Usuario as UsuarioModelo;
use Core\Domain\Usuario\Entities\Usuario as UsuarioEntidade;
use DateTimeImmutable;
use DateTimeInterface;

class UsuarioMapeador
{
    public function paraEntidade(UsuarioModelo $modelo): UsuarioEntidade
    {
        return new UsuarioEntidade(
            id: $modelo->getKey(),
            nome: $modelo->nome,
            email: $modelo->email,
            senha: $modelo->senha,
            email_verificado_em: $this->paraDataImutavel($modelo->email_verificado_em),
            remember_token: $modelo->remember_token,
            criado_em: $this->paraDataImutavel($modelo->criado_em),
            atualizado_em: $this->paraDataImutavel($modelo->atualizado_em),
            papeis: $modelo->exists ? $modelo->getRoleNames()->values()->all() : [],
        );
    }

    public function paraModelo(UsuarioEntidade $entidade, ?UsuarioModelo $modelo = null): UsuarioModelo
    {
        $modelo ??= new UsuarioModelo;
        $dados = [
            'nome' => $entidade->nome(),
            'email' => $entidade->email(),
        ];
        // Em atualizações sem nova senha, a entidade chega com senha vazia — não deve
        // sobrescrever o hash existente no banco.
        if ($entidade->senha() !== '') {
            $dados['senha'] = $entidade->senha();
        }
        $modelo->fill($dados);
        $modelo->remember_token = $entidade->tokenLembranca();
        $this->aplicar($modelo, $entidade->id(), [
            'email_verificado_em' => $entidade->emailVerificadoEm(),
            'criado_em' => $entidade->criadoEm(),
            'atualizado_em' => $entidade->atualizadoEm(),
        ]);

        return $modelo;
    }

    private function aplicar(UsuarioModelo $modelo, ?int $id, array $atributos): void
    {
        if ($id !== null) {
            $modelo->setAttribute($modelo->getKeyName(), $id);
        }
        foreach ($atributos as $campo => $valor) {
            if ($valor !== null) {
                $modelo->setAttribute($campo, $valor);
            }
        }
    }

    private function paraDataImutavel(mixed $data): ?DateTimeImmutable
    {
        if ($data === null) {
            return null;
        }

        return $data instanceof DateTimeInterface
            ? DateTimeImmutable::createFromInterface($data)
            : new DateTimeImmutable((string) $data);
    }
}
