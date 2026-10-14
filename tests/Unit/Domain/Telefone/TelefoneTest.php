<?php

namespace Tests\Unit\Domain\Telefone;

use Core\Domain\Telefone\ValueObjects\Telefone;
use PHPUnit\Framework\TestCase;

class TelefoneTest extends TestCase
{
    public function test_sanitiza_numero_removendo_caracteres_nao_numericos(): void
    {
        $telefone = new Telefone('(11) 98765-4321');

        $this->assertSame('11987654321', $telefone->numero());
    }

    public function test_formata_celular_com_onze_digitos(): void
    {
        $telefone = new Telefone('11987654321');

        $this->assertSame('(11) 98765-4321', $telefone->formatado());
    }

    public function test_formata_telefone_fixo_com_dez_digitos(): void
    {
        $telefone = new Telefone('1132654321');

        $this->assertSame('(11) 3265-4321', $telefone->formatado());
    }

    public function test_formata_numero_com_nove_digitos(): void
    {
        $telefone = new Telefone('987654321');

        $this->assertSame('98765-4321', $telefone->formatado());
    }

    public function test_formata_numero_com_oito_digitos(): void
    {
        $telefone = new Telefone('32654321');

        $this->assertSame('3265-4321', $telefone->formatado());
    }

    public function test_formata_numero_internacional_com_treze_digitos(): void
    {
        $telefone = new Telefone('5511987654321');

        $this->assertSame('+55 (11) 98765-4321', $telefone->formatado());
    }

    public function test_retorna_numero_sem_formatacao_quando_tamanho_nao_reconhecido(): void
    {
        $telefone = new Telefone('123');

        $this->assertSame('123', $telefone->formatado());
    }
}
