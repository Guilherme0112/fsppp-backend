<?php

namespace Core\Domain\SolicitacaoPagamento\Entities;

readonly class UsuarioResumo implements \JsonSerializable
{
    public function __construct(
        public int $id,
        public string $nome,
        public string $email,
    ) {}

    public function paraArray(): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'email' => $this->email,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->paraArray();
    }
}
