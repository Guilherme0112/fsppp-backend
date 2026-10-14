<?php

namespace Tests\Unit\Domain\Endereco;

use Core\Domain\Endereco\Entities\Endereco;
use PHPUnit\Framework\TestCase;

class EnderecoTest extends TestCase
{
    public function test_obtem_endereco_em_unica_linha_com_todos_os_campos(): void
    {
        $endereco = new Endereco(
            id: 1,
            logradouro: 'Rua das Flores',
            numero: '123',
            complemento: 'Apto 45',
            bairro: 'Centro',
            cidade: 'São Paulo',
            estado: 'SP',
            cep: '01000-000',
        );

        $this->assertSame(
            'Rua das Flores, 123 - Centro, Apto 45, São Paulo - SP',
            $endereco->obterEnderecoUnicaLinha(),
        );
    }

    public function test_obtem_endereco_em_unica_linha_com_campos_opcionais_nulos(): void
    {
        $endereco = new Endereco(
            id: null,
            logradouro: 'Rua das Flores',
            numero: '123',
            bairro: 'Centro',
            cidade: 'São Paulo',
            estado: 'SP',
        );

        $this->assertSame(
            'Rua das Flores, 123 - Centro, , São Paulo - SP',
            $endereco->obterEnderecoUnicaLinha(),
        );
    }

    public function test_cria_endereco_a_partir_do_formato_da_conexa(): void
    {
        $endereco = Endereco::criarDoConexa([
            'street' => 'Avenida Brasil',
            'number' => '500',
            'neighborhood' => 'Jardins',
            'city' => 'Belo Horizonte',
            'state' => ['abbreviation' => 'MG'],
            'additionalDetails' => 'Sala 2',
        ]);

        $this->assertNull($endereco->id());
        $this->assertSame('Avenida Brasil', $endereco->logradouro());
        $this->assertSame('500', $endereco->numero());
        $this->assertSame('Jardins', $endereco->bairro());
        $this->assertSame('Belo Horizonte', $endereco->cidade());
        $this->assertSame('MG', $endereco->estado());
        $this->assertSame('Sala 2', $endereco->complemento());
        $this->assertSame(
            'Avenida Brasil, 500 - Jardins, Sala 2, Belo Horizonte - MG',
            $endereco->obterEnderecoUnicaLinha(),
        );
    }

    public function test_cria_endereco_a_partir_do_formato_da_conexa_com_campos_ausentes(): void
    {
        $endereco = Endereco::criarDoConexa([]);

        $this->assertSame('', $endereco->logradouro());
        $this->assertSame('', $endereco->numero());
        $this->assertSame('', $endereco->bairro());
        $this->assertSame('', $endereco->cidade());
        $this->assertSame('', $endereco->estado());
        $this->assertSame('', $endereco->complemento());
    }
}
