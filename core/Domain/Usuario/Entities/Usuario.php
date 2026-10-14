<?php

namespace Core\Domain\Usuario\Entities;

class Usuario implements \JsonSerializable
{
    public function __construct(
        private readonly ?int $id,
        private readonly string $nome,
        private readonly string $email,
        private readonly string $senha,
        private readonly ?\DateTimeImmutable $email_verificado_em = null,
        private readonly ?string $remember_token = null,
        private readonly ?\DateTimeImmutable $criado_em = null,
        private readonly ?\DateTimeImmutable $atualizado_em = null,
        /** @var list<string> */
        private readonly array $papeis = [],
    ) {}

    public function id(): ?int
    {
        return $this->id;
    }

    public function nome(): string
    {
        return $this->nome;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function senha(): string
    {
        return $this->senha;
    }

    public function emailVerificadoEm(): ?\DateTimeImmutable
    {
        return $this->email_verificado_em;
    }

    public function tokenLembranca(): ?string
    {
        return $this->remember_token;
    }

    public function criadoEm(): ?\DateTimeImmutable
    {
        return $this->criado_em;
    }

    public function atualizadoEm(): ?\DateTimeImmutable
    {
        return $this->atualizado_em;
    }

    /** @return list<string> */
    public function papeis(): array
    {
        return $this->papeis;
    }

    public function paraArray(): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'email' => $this->email,
            'email_verificado_em' => $this->email_verificado_em?->format(DATE_ATOM),
            'criado_em' => $this->criado_em?->format(DATE_ATOM),
            'atualizado_em' => $this->atualizado_em?->format(DATE_ATOM),
            'papeis' => $this->papeis,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->paraArray();
    }
}
