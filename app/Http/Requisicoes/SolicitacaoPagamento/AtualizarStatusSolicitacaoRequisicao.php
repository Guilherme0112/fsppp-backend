<?php

namespace App\Http\Requisicoes\SolicitacaoPagamento;

use App\Http\Requisicoes\RequisicaoAutenticada;
use Core\Domain\SolicitacaoPagamento\Entities\SolicitacaoPagamento;
use Illuminate\Validation\Rule;

class AtualizarStatusSolicitacaoRequisicao extends RequisicaoAutenticada
{
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([
                SolicitacaoPagamento::STATUS_APROVADO,
                SolicitacaoPagamento::STATUS_CANCELADO,
            ])],
        ];
    }
}
