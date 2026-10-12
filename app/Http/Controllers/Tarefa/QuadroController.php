<?php

namespace App\Http\Controllers\Tarefa;

use App\Http\Controllers\Controller;
use App\Http\Requisicoes\Tarefa\ListarQuadrosRequisicao;
use App\Http\Requisicoes\Tarefa\SalvarQuadroRequisicao;
use App\Http\Requisicoes\UsuarioAutenticadoRequisicao;
use Core\Application\Tarefa\DTOs\AtualizarQuadroDTO;
use Core\Application\Tarefa\DTOs\CriarQuadroDTO;
use Core\Application\Tarefa\DTOs\ListarQuadrosDTO;
use Core\Application\Tarefa\UseCases\ApagarQuadroUseCase;
use Core\Application\Tarefa\UseCases\AtualizarQuadroUseCase;
use Core\Application\Tarefa\UseCases\CriarQuadroUseCase;
use Core\Application\Tarefa\UseCases\ListarQuadrosPorUsuarioIdUseCase;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class QuadroController extends Controller
{
    #[OA\Get(
        path: "/quadros",
        summary: "Listar quadros do usuário",
        tags: ["Quadros"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista de quadros",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", type: "integer"),
                            new OA\Property(property: "nome", type: "string"),
                            new OA\Property(property: "descricao", type: "string", nullable: true),
                            new OA\Property(property: "usuario_id", type: "integer"),
                        ]
                    )
                )
            ),
            new OA\Response(response: 401, description: "Não autenticado")
        ]
    )]
    public function listarQuadros(
        ListarQuadrosRequisicao $requisicao,
        ListarQuadrosPorUsuarioIdUseCase $casoDeUso,
    ): JsonResponse {
        return response()->json($casoDeUso->executar(
            ListarQuadrosDTO::daRequisicao($requisicao->validated(), $requisicao->usuarioId()),
        ));
    }

    #[OA\Post(
        path: "/quadros",
        summary: "Criar novo quadro",
        tags: ["Quadros"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "nome", type: "string", example: "Meu Quadro"),
                    new OA\Property(property: "descricao", type: "string", example: "Descrição do quadro", nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Quadro criado com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer"),
                        new OA\Property(property: "nome", type: "string"),
                        new OA\Property(property: "descricao", type: "string", nullable: true),
                        new OA\Property(property: "usuario_id", type: "integer"),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Não autenticado"),
            new OA\Response(response: 422, description: "Erro de validação")
        ]
    )]
    public function criarQuadro(
        SalvarQuadroRequisicao $requisicao,
        CriarQuadroUseCase $casoDeUso,
    ): JsonResponse {
        return response()->json($casoDeUso->executar(
            CriarQuadroDTO::daRequisicao($requisicao->validated(), $requisicao->usuarioId()),
        ), 201);
    }

    #[OA\Put(
        path: "/quadros/{id}",
        summary: "Editar quadro existente",
        tags: ["Quadros"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "nome", type: "string", example: "Nome Atualizado"),
                    new OA\Property(property: "descricao", type: "string", example: "Descrição atualizada", nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Quadro atualizado com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer"),
                        new OA\Property(property: "nome", type: "string"),
                        new OA\Property(property: "descricao", type: "string", nullable: true),
                        new OA\Property(property: "usuario_id", type: "integer"),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Não autenticado"),
            new OA\Response(response: 404, description: "Quadro não encontrado"),
            new OA\Response(response: 422, description: "Erro de validação")
        ]
    )]
    public function editarQuadro(
        SalvarQuadroRequisicao $requisicao,
        int $id,
        AtualizarQuadroUseCase $casoDeUso,
    ): JsonResponse {
        return response()->json($casoDeUso->executar(
            AtualizarQuadroDTO::daRequisicao($requisicao->validated(), $id, $requisicao->usuarioId()),
        ));
    }

    #[OA\Delete(
        path: "/quadros/{id}",
        summary: "Apagar quadro",
        tags: ["Quadros"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 204, description: "Quadro apagado com sucesso"),
            new OA\Response(response: 401, description: "Não autenticado"),
            new OA\Response(response: 404, description: "Quadro não encontrado")
        ]
    )]
    public function apagarQuadro(
        UsuarioAutenticadoRequisicao $requisicao,
        int $id,
        ApagarQuadroUseCase $casoDeUso,
    ): JsonResponse {
        $casoDeUso->executar($id, $requisicao->usuarioId());

        return response()->json(null, 204);
    }
}
