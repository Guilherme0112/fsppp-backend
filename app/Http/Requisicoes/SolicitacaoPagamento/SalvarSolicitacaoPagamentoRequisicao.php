<?php

namespace App\Http\Requisicoes\SolicitacaoPagamento;

use App\Http\Requisicoes\RequisicaoAutenticada;

class SalvarSolicitacaoPagamentoRequisicao extends RequisicaoAutenticada
{
    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:255'],
            'valor' => ['required', 'integer', 'min:1'],
            'descricao' => ['nullable', 'string'],
        ];
    }
}
