<?php

namespace Core\Domain\Shared\ValueObjects;

final class NumeroDocumento
{
    private readonly string $numero;

    public function __construct(?string $numero)
    {
        $this->numero = $numero !== null ? preg_replace('/\D/', '', $numero) : '';
    }

    public function numero(): string
    {
        return $this->numero;
    }

    public function tipo(): string
    {
        if ($this->numero === '') {
            return '';
        }

        return strlen($this->numero) <= 11 ? 'cpf' : 'cnpj';
    }
}
