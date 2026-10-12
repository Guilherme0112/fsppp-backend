<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Http\Requisicoes\Usuario\SalvarUsuarioRequisicao;
use App\Http\Requisicoes\UsuarioAutenticadoRequisicao;
use Core\Application\Usuario\DTOs\AtualizarUsuarioDTO;
use Core\Application\Usuario\DTOs\CriarUsuarioDTO;
use Core\Application\Usuario\DTOs\ListarUsuariosDTO;
use Core\Application\Usuario\UseCases\ApagarUsuarioUseCase;
use Core\Application\Usuario\UseCases\AtualizarUsuarioUseCase;
use Core\Application\Usuario\UseCases\CriarUsuarioUseCase;
use Core\Application\Usuario\UseCases\ListarUsuariosUseCase;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class UsuarioController extends Controller
{
    #[OA\Get(
        path: '/usuarios',
        summary: 'Listar usuários',
        tags: ['Usuários'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de usuários',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer'),
                            new OA\Property(property: 'nome', type: 'string'),
                            new OA\Property(property: 'email', type: 'string'),
                            new OA\Property(property: 'criado_em', type: 'string', format: 'date-time', nullable: true),
                            new OA\Property(property: 'atualizado_em', type: 'string', format: 'date-time', nullable: true),
                        ]
                    )
                )
            ),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function listarUsuarios(
        UsuarioAutenticadoRequisicao $requisicao,
        ListarUsuariosUseCase $casoDeUso,
    ): JsonResponse {
        return response()->json($casoDeUso->executar(
            ListarUsuariosDTO::daRequisicao($requisicao->validated(), $requisicao->usuarioId()),
        ));
    }

    #[OA\Post(
        path: '/usuarios',
        summary: 'Criar novo usuário',
        tags: ['Usuários'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'nome', type: 'string', example: 'João Silva'),
                    new OA\Property(property: 'email', type: 'string', example: 'joao@example.com'),
                    new OA\Property(property: 'senha', type: 'string', example: 'secret123'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Usuário criado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'nome', type: 'string'),
                        new OA\Property(property: 'email', type: 'string'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 422, description: 'Erro de validação'),
        ]
    )]
    public function criarUsuario(
        SalvarUsuarioRequisicao $requisicao,
        CriarUsuarioUseCase $casoDeUso,
    ): JsonResponse {
        return response()->json($casoDeUso->executar(
            CriarUsuarioDTO::daRequisicao($requisicao->validated()),
        ), 201);
    }

    #[OA\Put(
        path: '/usuarios/{id}',
        summary: 'Atualizar usuário existente',
        tags: ['Usuários'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'nome', type: 'string', example: 'João Atualizado'),
                    new OA\Property(property: 'email', type: 'string', example: 'joao@example.com'),
                    new OA\Property(property: 'senha', type: 'string', example: 'novasenha123', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Usuário atualizado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'nome', type: 'string'),
                        new OA\Property(property: 'email', type: 'string'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Usuário não encontrado'),
            new OA\Response(response: 422, description: 'Erro de validação'),
        ]
    )]
    public function atualizarUsuario(
        SalvarUsuarioRequisicao $requisicao,
        int $id,
        AtualizarUsuarioUseCase $casoDeUso,
    ): JsonResponse {
        return response()->json($casoDeUso->executar(
            AtualizarUsuarioDTO::daRequisicao($requisicao->validated(), $id),
        ));
    }

    #[OA\Delete(
        path: '/usuarios/{id}',
        summary: 'Apagar usuário',
        tags: ['Usuários'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Usuário apagado com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Usuário não encontrado'),
        ]
    )]
    public function apagarUsuario(
        UsuarioAutenticadoRequisicao $requisicao,
        int $id,
        ApagarUsuarioUseCase $casoDeUso,
    ): JsonResponse {
        $casoDeUso->executar($id);

        return response()->json(null, 204);
    }
}
