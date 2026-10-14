<?php

namespace App\Http\Requisicoes\Usuario;

use App\Http\Requisicoes\RequisicaoAutenticada;

class ListarUsuariosRequisicao extends RequisicaoAutenticada
{
    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }
}
