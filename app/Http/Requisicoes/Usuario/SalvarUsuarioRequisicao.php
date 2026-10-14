<?php

namespace App\Http\Requisicoes\Usuario;

use App\Http\Requisicoes\RequisicaoAutenticada;

class SalvarUsuarioRequisicao extends RequisicaoAutenticada
{
    public function rules(): array
    {
        return [
            'nome'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'senha' => ['nullable', 'string', 'min:6'],
            'papeis' => ['nullable', 'array'],
            'papeis.*' => ['string', 'exists:roles,name'],
        ];
    }
}
