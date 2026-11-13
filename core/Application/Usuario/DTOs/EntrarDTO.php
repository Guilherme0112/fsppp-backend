<?php

namespace Core\Application\Usuario\DTOs;

class EntrarDTO
{
    public function __construct(
        public string $email,
        public string $senha,
        public string $nomeDispositivo,
    ) {}

    public static function deArray(array $dados): self
    {
        return new self(
            email: $dados['email'],
            senha: $dados['senha'],
            nomeDispositivo: $dados['nome_dispositivo'] ?? 'api',
        );
    }
}
