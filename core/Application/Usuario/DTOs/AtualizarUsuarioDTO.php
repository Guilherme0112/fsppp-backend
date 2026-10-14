<?php

namespace Core\Application\Usuario\DTOs;

use Core\Domain\Usuario\Entities\Usuario;

readonly class AtualizarUsuarioDTO
{
    public function __construct(
        public int $id,
        public string $nome,
        public string $email,
        public ?string $senha = null,
        /** @var list<string> */
        public array $papeis = [],
    ) {}

    public static function daRequisicao(array $dados, int $id): self
    {
        return new self(
            id: $id,
            nome: $dados['nome'],
            email: $dados['email'],
            senha: $dados['senha'] ?? null,
            papeis: $dados['papeis'] ?? [],
        );
    }

    public function paraEntidade(): Usuario
    {
        return new Usuario(
            id: $this->id,
            nome: $this->nome,
            email: $this->email,
            senha: $this->senha ?? '',
            papeis: $this->papeis,
        );
    }
}
