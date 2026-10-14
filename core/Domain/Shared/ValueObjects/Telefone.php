<?php

namespace Core\Domain\Shared\ValueObjects;

final class Telefone
{
    private readonly string $numero;

    public function __construct(string $telefone)
    {
        $this->numero = preg_replace('/[^0-9]/', '', $telefone);
    }

    public function numero(): string
    {
        return $this->numero;
    }

    public function formatado(): string
    {
        return match (strlen($this->numero)) {
            11 => preg_replace('/^(\d{2})(\d{5})(\d{4})$/', '($1) $2-$3', $this->numero),
            10 => preg_replace('/^(\d{2})(\d{4})(\d{4})$/', '($1) $2-$3', $this->numero),
            9 => preg_replace('/^(\d{5})(\d{4})$/', '$1-$2', $this->numero),
            8 => preg_replace('/^(\d{4})(\d{4})$/', '$1-$2', $this->numero),
            13 => preg_replace('/^(\d{2})(\d{2})(\d{5})(\d{4})$/', '+$1 ($2) $3-$4', $this->numero),
            default => $this->numero,
        };
    }
}
