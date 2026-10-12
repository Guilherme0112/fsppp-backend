<?php

namespace Core\Application\Usuario\DTOs;

use Core\Domain\Usuario\Entities\Usuario;

readonly class CriarUsuarioDTO
{
    public function __construct(
        public string $nome,
        public string $email,
        public string $senha,
    ) {}

    public static function daRequisicao(array $dados): self
    {
        return new self(
            nome: $dados['nome'],
            email: $dados['email'],
            senha: $dados['senha'],
        );
    }

    public function paraEntidade(): Usuario
    {
        return new Usuario(
            id: null,
            nome: $this->nome,
            email: $this->email,
            senha: $this->senha,
        );
    }
}
