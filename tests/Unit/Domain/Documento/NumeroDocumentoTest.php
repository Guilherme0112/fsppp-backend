<?php

namespace Tests\Unit\Domain\Documento;

use Core\Domain\Documento\ValueObjects\NumeroDocumento;
use PHPUnit\Framework\TestCase;

class NumeroDocumentoTest extends TestCase
{
    public function test_sanitiza_numero_removendo_caracteres_nao_numericos(): void
    {
        $documento = new NumeroDocumento('123.456.789-09');

        $this->assertSame('12345678909', $documento->numero());
    }

    public function test_numero_nulo_resulta_em_string_vazia(): void
    {
        $documento = new NumeroDocumento(null);

        $this->assertSame('', $documento->numero());
    }

    public function test_tipo_retorna_cpf_para_numero_com_onze_digitos_ou_menos(): void
    {
        $documento = new NumeroDocumento('123.456.789-09');

        $this->assertSame('cpf', $documento->tipo());
    }

    public function test_tipo_retorna_cnpj_para_numero_com_mais_de_onze_digitos(): void
    {
        $documento = new NumeroDocumento('12.345.678/0001-95');

        $this->assertSame('cnpj', $documento->tipo());
    }

    public function test_tipo_retorna_string_vazia_quando_numero_nulo_ou_vazio(): void
    {
        $this->assertSame('', (new NumeroDocumento(null))->tipo());
        $this->assertSame('', (new NumeroDocumento(''))->tipo());
    }

    public function test_formatado_aplica_mascara_de_cnpj(): void
    {
        $documento = new NumeroDocumento('12345678000195');

        $this->assertSame('12.345.678/0001-95', $documento->formatado());
    }

    public function test_formatado_ignora_mascara_quando_numero_nao_possui_quatorze_digitos(): void
    {
        $documento = new NumeroDocumento('12345678909');

        $this->assertSame('12345678909', $documento->formatado());
    }

    public function test_formatado_retorna_string_vazia_quando_numero_nulo(): void
    {
        $documento = new NumeroDocumento(null);

        $this->assertSame('', $documento->formatado());
    }
}
