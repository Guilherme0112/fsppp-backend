<?php

namespace App\Http\Requisicoes\SolicitacaoCompra;

use App\Http\Requisicoes\RequisicaoAutenticada;
use Illuminate\Validation\Rule;

class AvaliarDiretoriaRequisicao extends RequisicaoAutenticada
{
    public function rules(): array
    {
        return [
            'aprovado' => ['required', 'boolean'],
            'prioridade' => ['required', Rule::in(['baixa', 'media', 'alta', 'urgente'])],
            'motivo_rejeicao' => ['nullable', 'string', 'required_if:aprovado,false'],
        ];
    }
}
