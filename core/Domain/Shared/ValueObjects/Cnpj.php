<?php

namespace Core\Domain\Shared\ValueObjects;

final class Cnpj
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

    public function obterNumeroFormatado(): string
    {
        if (strlen($this->numero) !== 14) {
            return $this->numero;
        }

        return substr($this->numero, 0, 2).'.'.
            substr($this->numero, 2, 3).'.'.
            substr($this->numero, 5, 3).'/'.
            substr($this->numero, 8, 4).'-'.
            substr($this->numero, 12, 2);
    }
}
