<?php

namespace App\Mappers\Solicitacao;

use Carbon\Carbon;
use Core\Domain\Endereco\Entities\Endereco;
use Core\Domain\Shared\ValueObjects\Cnpj;
use Core\Domain\Shared\ValueObjects\NumeroDocumento;

class CancelamentoMapper
{
    public function __construct(
        protected string $nome_unidade,
        protected string $cnpj_unidade,
        protected string $endereco_cliente,
        protected string $razao_social_cliente,
        protected string $tipo_documento_cliente,
        protected string $cpf_cnpj_cliente,
        protected string $local_data,
    ) {}

    /**
     * Prepara e injeta os dados exatamente com as chaves que o Blade espera
     */
    public function render(): array
    {
        return [
            'nome_unidade' => $this->nome_unidade,
            'cnpj_unidade' => $this->cnpj_unidade,
            'endereco_cliente' => $this->endereco_cliente,
            'razao_social_cliente' => $this->razao_social_cliente,
            'tipo_documento_cliente' => $this->tipo_documento_cliente,
            'cpf_cnpj_cliente' => $this->cpf_cnpj_cliente,
            'local_data' => $this->local_data,
        ];
    }

    public static function build(array $company, array $customer): self
    {
        $cidade = $company['address']['city'] ?? '';
        $estado = $company['address']['state']['abbreviation'] ?? '';
        $dataExtenso = Carbon::now()->locale('pt_BR')->translatedFormat('d \d\e F \d\e Y');
        $localData = "{$cidade}/{$estado}, {$dataExtenso}";

        $documentoCliente = $customer['cpf'] ?? $customer['legalPerson']['cnpj'] ?? $customer['naturalPerson']['cpf'] ?? '';

        return new self(
            nome_unidade: $company['tradeName'] ?? $company['legalName'],
            cnpj_unidade: (new Cnpj($company['cnpj']))->obterNumeroFormatado(),
            endereco_cliente: Endereco::criarDoConexa($customer['address'])->obterEnderecoUnicaLinha(),
            razao_social_cliente: $customer['name'] ?? $customer['firstName'],
            tipo_documento_cliente: strtoupper((new NumeroDocumento($documentoCliente))->tipo()),
            cpf_cnpj_cliente: $documentoCliente,
            local_data: $localData
        );
    }
}
