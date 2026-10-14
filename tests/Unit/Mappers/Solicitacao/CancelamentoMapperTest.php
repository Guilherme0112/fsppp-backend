<?php

namespace Tests\Unit\Mappers\Solicitacao;

use App\Mappers\Solicitacao\CancelamentoMapper;
use PHPUnit\Framework\TestCase;

class CancelamentoMapperTest extends TestCase
{
    public function test_build_monta_dados_a_partir_de_pessoa_fisica(): void
    {
        $company = [
            'tradeName' => 'Unidade Centro',
            'cnpj' => '12345678000195',
            'address' => [
                'city' => 'São Paulo',
                'state' => ['abbreviation' => 'SP'],
            ],
        ];

        $customer = [
            'name' => 'João da Silva',
            'cpf' => '123.456.789-09',
            'address' => [
                'street' => 'Rua das Flores',
                'number' => '123',
                'neighborhood' => 'Centro',
                'city' => 'São Paulo',
                'state' => ['abbreviation' => 'SP'],
                'additionalDetails' => 'Apto 45',
            ],
        ];

        $dados = CancelamentoMapper::build($company, $customer)->render();

        $this->assertSame('Unidade Centro', $dados['nome_unidade']);
        $this->assertSame('12.345.678/0001-95', $dados['cnpj_unidade']);
        $this->assertSame('Rua das Flores, 123 - Centro, Apto 45, São Paulo - SP', $dados['endereco_cliente']);
        $this->assertSame('João da Silva', $dados['razao_social_cliente']);
        $this->assertSame('CPF', $dados['tipo_documento_cliente']);
        $this->assertSame('123.456.789-09', $dados['cpf_cnpj_cliente']);
        $this->assertStringStartsWith('São Paulo/SP, ', $dados['local_data']);
    }

    public function test_build_monta_dados_a_partir_de_pessoa_juridica(): void
    {
        $company = [
            'legalName' => 'Unidade Centro LTDA',
            'cnpj' => '12345678000195',
            'address' => [
                'city' => 'São Paulo',
                'state' => ['abbreviation' => 'SP'],
            ],
        ];

        $customer = [
            'firstName' => 'Empresa Cliente',
            'legalPerson' => ['cnpj' => '98765432000110'],
            'address' => [
                'street' => 'Avenida Brasil',
                'number' => '500',
                'neighborhood' => 'Jardins',
                'city' => 'Belo Horizonte',
                'state' => ['abbreviation' => 'MG'],
            ],
        ];

        $dados = CancelamentoMapper::build($company, $customer)->render();

        $this->assertSame('Unidade Centro LTDA', $dados['nome_unidade']);
        $this->assertSame('Empresa Cliente', $dados['razao_social_cliente']);
        $this->assertSame('CNPJ', $dados['tipo_documento_cliente']);
        $this->assertSame('98765432000110', $dados['cpf_cnpj_cliente']);
        $this->assertSame('Avenida Brasil, 500 - Jardins, , Belo Horizonte - MG', $dados['endereco_cliente']);
    }
}
