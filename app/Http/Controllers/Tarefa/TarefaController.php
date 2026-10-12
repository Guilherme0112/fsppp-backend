<?php

namespace App\Http\Controllers\Tarefa;

use App\Http\Controllers\Controller;
use App\Http\Requisicoes\Tarefa\ListarTarefasRequisicao;
use App\Http\Requisicoes\Tarefa\SalvarTarefaRequisicao;
use App\Http\Requisicoes\UsuarioAutenticadoRequisicao;
use Core\Application\Tarefa\DTOs\AtualizarTarefaDTO;
use Core\Application\Tarefa\DTOs\CriarTarefaDTO;
use Core\Application\Tarefa\DTOs\ListarTarefasDTO;
use Core\Application\Tarefa\UseCases\ApagarTarefaUseCase;
use Core\Application\Tarefa\UseCases\AtualizarTarefaUseCase;
use Core\Application\Tarefa\UseCases\CriarTarefaUseCase;
use Core\Application\Tarefa\UseCases\ListarTarefasUseCase;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class TarefaController extends Controller
{
    #[OA\Get(
        path: "/tarefas",
        summary: "Listar tarefas do usuário",
        tags: ["Tarefas"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista de tarefas",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", type: "integer"),
                            new OA\Property(property: "nome", type: "string"),
                            new OA\Property(property: "descricao", type: "string", nullable: true),
                            new OA\Property(property: "status_id", type: "integer"),
                            new OA\Property(property: "quadro_id", type: "integer"),
                            new OA\Property(property: "data_inicio", type: "string", format: "date", nullable: true),
                            new OA\Property(property: "data_fim", type: "string", format: "date", nullable: true),
                        ]
                    )
                )
            ),
            new OA\Response(response: 401, description: "Não autenticado")
        ]
    )]
    public function listarTarefas(
        ListarTarefasRequisicao $requisicao,
        ListarTarefasUseCase $casoDeUso,
    ): JsonResponse {
        return response()->json($casoDeUso->executar(
            ListarTarefasDTO::daRequisicao($requisicao->validated(), $requisicao->usuarioId()),
        ));
    }

    #[OA\Post(
        path: "/tarefas",
        summary: "Criar nova tarefa",
        tags: ["Tarefas"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "nome", type: "string", example: "Minha Tarefa"),
                    new OA\Property(property: "descricao", type: "string", example: "Detalhes da tarefa", nullable: true),
                    new OA\Property(property: "status_id", type: "integer", example: 1),
                    new OA\Property(property: "quadro_id", type: "integer", example: 1),
                    new OA\Property(property: "data_inicio", type: "string", format: "date", example: "2026-10-11", nullable: true),
                    new OA\Property(property: "data_fim", type: "string", format: "date", example: "2026-10-12", nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Tarefa criada com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer"),
                        new OA\Property(property: "nome", type: "string"),
                        new OA\Property(property: "descricao", type: "string", nullable: true),
                        new OA\Property(property: "status_id", type: "integer"),
                        new OA\Property(property: "quadro_id", type: "integer"),
                        new OA\Property(property: "data_inicio", type: "string", format: "date", nullable: true),
                        new OA\Property(property: "data_fim", type: "string", format: "date", nullable: true),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Não autenticado"),
            new OA\Response(response: 422, description: "Erro de validação")
        ]
    )]
    public function criarTarefa(
        SalvarTarefaRequisicao $requisicao,
        CriarTarefaUseCase $casoDeUso,
    ): JsonResponse {
        return response()->json($casoDeUso->executar(
            CriarTarefaDTO::daRequisicao($requisicao->validated(), $requisicao->usuarioId()),
        ), 201);
    }

    #[OA\Put(
        path: "/tarefas/{id}",
        summary: "Atualizar tarefa existente",
        tags: ["Tarefas"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "nome", type: "string", example: "Tarefa Atualizada"),
                    new OA\Property(property: "descricao", type: "string", example: "Novos detalhes", nullable: true),
                    new OA\Property(property: "status_id", type: "integer", example: 2),
                    new OA\Property(property: "quadro_id", type: "integer", example: 1),
                    new OA\Property(property: "data_inicio", type: "string", format: "date", example: "2026-10-11", nullable: true),
                    new OA\Property(property: "data_fim", type: "string", format: "date", example: "2026-10-13", nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Tarefa atualizada com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer"),
                        new OA\Property(property: "nome", type: "string"),
                        new OA\Property(property: "descricao", type: "string", nullable: true),
                        new OA\Property(property: "status_id", type: "integer"),
                        new OA\Property(property: "quadro_id", type: "integer"),
                        new OA\Property(property: "data_inicio", type: "string", format: "date", nullable: true),
                        new OA\Property(property: "data_fim", type: "string", format: "date", nullable: true),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Não autenticado"),
            new OA\Response(response: 404, description: "Tarefa não encontrada"),
            new OA\Response(response: 422, description: "Erro de validação")
        ]
    )]
    public function atualizarTarefa(
        SalvarTarefaRequisicao $requisicao,
        int $id,
        AtualizarTarefaUseCase $casoDeUso,
    ): JsonResponse {
        return response()->json($casoDeUso->executar(
            AtualizarTarefaDTO::daRequisicao($requisicao->validated(), $id, $requisicao->usuarioId()),
        ));
    }

    #[OA\Delete(
        path: "/tarefas/{id}",
        summary: "Apagar tarefa",
        tags: ["Tarefas"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 204, description: "Tarefa apagada com sucesso"),
            new OA\Response(response: 401, description: "Não autenticado"),
            new OA\Response(response: 404, description: "Tarefa não encontrada")
        ]
    )]
    public function apagarTarefa(
        UsuarioAutenticadoRequisicao $requisicao,
        int $id,
        ApagarTarefaUseCase $casoDeUso,
    ): JsonResponse {
        $casoDeUso->executar($id, $requisicao->usuarioId());
        return response()->json(null, 204);
    }
}
