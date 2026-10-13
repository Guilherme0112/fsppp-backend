<?php

namespace App\Http\Requisicoes\Tarefa;

use App\Http\Requisicoes\RequisicaoAutenticada;

class ListarTarefasRequisicao extends RequisicaoAutenticada
{
    public function rules(): array
    {
        return [
            'quadro_id' => ['required', 'integer', 'exists:quadros,id'],
        ];
    }
}
