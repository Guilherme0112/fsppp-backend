<?php

namespace App\Http\Requisicoes\SolicitacaoCompra;

use App\Http\Requisicoes\RequisicaoAutenticada;

class AtualizarStatusCompraRequisicao extends RequisicaoAutenticada
{
    public function rules(): array
    {
        return [
            'status_compra' => ['required', 'string', 'max:255'],
        ];
    }
}
