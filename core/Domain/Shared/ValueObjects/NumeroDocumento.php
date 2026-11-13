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

    public function formatado(): string
    {
        if ($this->numero === '') {
            return '';
        }

        if (strlen($this->numero) === 14) {
            return preg_replace('/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/', '$1.$2.$3/$4-$5', $this->numero);
        }

        return $this->numero;
    }
}
