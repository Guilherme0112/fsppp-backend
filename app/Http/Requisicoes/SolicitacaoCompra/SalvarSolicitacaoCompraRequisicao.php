<?php

namespace App\Http\Requisicoes\SolicitacaoCompra;

use App\Http\Requisicoes\RequisicaoAutenticada;

class SalvarSolicitacaoCompraRequisicao extends RequisicaoAutenticada
{
    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:255'],
            'valor' => ['required', 'integer', 'min:1'],
            'descricao' => ['nullable', 'string'],
            'prioridade' => ['sometimes', 'string', 'in:baixa,media,alta,urgente'],
            'prazo' => ['nullable', 'date'],
        ];
    }
}
