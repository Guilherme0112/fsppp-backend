<?php

namespace App\Http\Requisicoes\SolicitacaoCompra;

use App\Http\Requisicoes\RequisicaoAutenticada;
use Core\Domain\SolicitacaoCompra\Entities\SolicitacaoCompra;
use Illuminate\Validation\Rule;

class ListarTodasSolicitacoesRequisicao extends RequisicaoAutenticada
{
    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'status' => ['sometimes', Rule::in([
                SolicitacaoCompra::STATUS_PENDENTE,
                SolicitacaoCompra::STATUS_ENVIADO_DIRETORIA,
                SolicitacaoCompra::STATUS_APROVADO_DIRETORIA,
                SolicitacaoCompra::STATUS_REJEITADO_DIRETORIA,
                SolicitacaoCompra::STATUS_ANALISE_PROJETOS,
                SolicitacaoCompra::STATUS_RECURSOS_DISPONIVEIS_COMPRAS,
                SolicitacaoCompra::STATUS_RECURSOS_FUNDACAO,
                SolicitacaoCompra::STATUS_APROVADO,
                SolicitacaoCompra::STATUS_CONCLUIDO,
                SolicitacaoCompra::STATUS_CANCELADO,
            ])],
            'usuario_id' => ['sometimes', 'integer', 'min:1'],
            'busca' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}
