<?php

namespace App\Http\Requisicoes\SolicitacaoPagamento;

use App\Http\Requisicoes\RequisicaoAutenticada;
use Core\Domain\SolicitacaoPagamento\Entities\SolicitacaoPagamento;
use Illuminate\Validation\Rule;

class ListarTodasSolicitacoesRequisicao extends RequisicaoAutenticada
{
    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'status' => ['sometimes', Rule::in([
                SolicitacaoPagamento::STATUS_PENDENTE,
                SolicitacaoPagamento::STATUS_APROVADO,
                SolicitacaoPagamento::STATUS_CONCLUIDO,
                SolicitacaoPagamento::STATUS_CANCELADO,
            ])],
            'usuario_id' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
