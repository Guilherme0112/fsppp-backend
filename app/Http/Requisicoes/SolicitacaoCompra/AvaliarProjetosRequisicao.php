<?php

namespace App\Http\Requisicoes\SolicitacaoCompra;

use App\Http\Requisicoes\RequisicaoAutenticada;

class AvaliarProjetosRequisicao extends RequisicaoAutenticada
{
    public function rules(): array
    {
        return [
            'recursos_existem' => ['required', 'boolean'],
        ];
    }
}
