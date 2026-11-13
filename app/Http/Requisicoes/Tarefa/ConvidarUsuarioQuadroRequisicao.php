<?php

namespace App\Http\Requisicoes\Tarefa;

use App\Http\Requisicoes\RequisicaoAutenticada;

class ConvidarUsuarioQuadroRequisicao extends RequisicaoAutenticada
{
    public function rules(): array
    {
        return [
            'usuario_id' => ['required', 'integer', 'exists:usuarios,id'],
        ];
    }
}
