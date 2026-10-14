<?php

namespace App\Http\Requisicoes\SolicitacaoPagamento;

use App\Http\Requisicoes\RequisicaoAutenticada;

class ListarSolicitacoesRequisicao extends RequisicaoAutenticada
{
    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }
}
