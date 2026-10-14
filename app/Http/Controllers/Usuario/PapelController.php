<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Http\Requisicoes\UsuarioAutenticadoRequisicao;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Spatie\Permission\Models\Role;

class PapelController extends Controller
{
    #[OA\Get(
        path: '/papeis',
        summary: 'Listar papéis (roles) disponíveis para atribuição a usuários',
        security: [['bearerAuth' => []]],
        tags: ['Usuários'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de nomes de papéis',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(type: 'string'))
            ),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function listarPapeis(UsuarioAutenticadoRequisicao $requisicao): JsonResponse
    {
        return response()->json(
            Role::query()->orderBy('name')->pluck('name')
        );
    }
}
