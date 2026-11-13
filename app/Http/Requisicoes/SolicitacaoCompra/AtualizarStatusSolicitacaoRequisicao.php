<?php

namespace App\Http\Requisicoes\SolicitacaoCompra;

use App\Http\Requisicoes\RequisicaoAutenticada;
use Core\Domain\SolicitacaoCompra\Entities\SolicitacaoCompra;
use Illuminate\Validation\Rule;

class AtualizarStatusSolicitacaoRequisicao extends RequisicaoAutenticada
{
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([
                SolicitacaoCompra::STATUS_APROVADO,
                SolicitacaoCompra::STATUS_CANCELADO,
            ])],
        ];
    }
}
