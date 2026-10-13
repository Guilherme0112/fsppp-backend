<?php

namespace App\Http\Controllers\Tarefa;

use App\Http\Controllers\Controller;
use App\Http\Requisicoes\Tarefa\SalvarStatusQuadroRequisicao;
use App\Http\Requisicoes\UsuarioAutenticadoRequisicao;
use Core\Application\Tarefa\DTOs\AtualizarStatusQuadroDTO;
use Core\Application\Tarefa\DTOs\CriarStatusQuadroDTO;
use Core\Application\Tarefa\UseCases\ApagarStatusQuadroUseCase;
use Core\Application\Tarefa\UseCases\AtualizarStatusQuadroUseCase;
use Core\Application\Tarefa\UseCases\CriarStatusQuadroUseCase;
use Core\Application\Tarefa\UseCases\ListarStatusQuadroUseCase;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class StatusQuadroController extends Controller
{
    #[OA\Get(
        path: '/status-quadro/{quadroId}',
        summary: 'Listar status de um quadro',
        security: [['bearerAuth' => []]],
        tags: ['Status do Quadro'],
        parameters: [
            new OA\Parameter(name: 'quadroId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de status do quadro',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer'),
                            new OA\Property(property: 'nome', type: 'string'),
                            new OA\Property(property: 'descricao', type: 'string', nullable: true),
                            new OA\Property(property: 'quadro_id', type: 'integer'),
                        ]
                    )
                )
            ),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Quadro não encontrado'),
        ]
    )]
    public function listarStatusPorQuadro(
        UsuarioAutenticadoRequisicao $requisicao,
        int $quadroId,
        ListarStatusQuadroUseCase $casoDeUso,
    ): JsonResponse {
        return response()->json($casoDeUso->executar($quadroId, $requisicao->usuarioId()));
    }

    #[OA\Post(
        path: '/status-quadro/{quadroId}',
        summary: 'Criar novo status para o quadro',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'nome', type: 'string', example: 'A Fazer'),
                    new OA\Property(property: 'descricao', type: 'string', example: 'Tarefas pendentes', nullable: true),
                ]
            )
        ),
        tags: ['Status do Quadro'],
        parameters: [
            new OA\Parameter(name: 'quadroId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Status criado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'nome', type: 'string'),
                        new OA\Property(property: 'descricao', type: 'string', nullable: true),
                        new OA\Property(property: 'quadro_id', type: 'integer'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Quadro não encontrado'),
            new OA\Response(response: 422, description: 'Erro de validação'),
        ]
    )]
    public function criarStatus(
        SalvarStatusQuadroRequisicao $requisicao,
        int $quadroId,
        CriarStatusQuadroUseCase $casoDeUso,
    ): JsonResponse {
        return response()->json($casoDeUso->executar(
            CriarStatusQuadroDTO::daRequisicao($requisicao->validated(), $quadroId, $requisicao->usuarioId()),
        ), 201);
    }

    #[OA\Put(
        path: '/status-quadro/{quadroId}/{id}',
        summary: 'Editar status de um quadro',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'nome', type: 'string', example: 'Em Andamento'),
                    new OA\Property(property: 'descricao', type: 'string', example: 'Tarefas sendo executadas', nullable: true),
                ]
            )
        ),
        tags: ['Status do Quadro'],
        parameters: [
            new OA\Parameter(name: 'quadroId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Status atualizado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'nome', type: 'string'),
                        new OA\Property(property: 'descricao', type: 'string', nullable: true),
                        new OA\Property(property: 'quadro_id', type: 'integer'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Status ou Quadro não encontrado'),
            new OA\Response(response: 422, description: 'Erro de validação'),
        ]
    )]
    public function editarStatus(
        SalvarStatusQuadroRequisicao $requisicao,
        int $quadroId,
        int $id,
        AtualizarStatusQuadroUseCase $casoDeUso,
    ): JsonResponse {
        return response()->json($casoDeUso->executar(
            AtualizarStatusQuadroDTO::daRequisicao(
                $requisicao->validated(),
                $quadroId,
                $id,
                $requisicao->usuarioId(),
            ),
        ));
    }

    #[OA\Delete(
        path: '/status-quadro/{quadroId}/{id}',
        summary: 'Apagar status de um quadro',
        security: [['bearerAuth' => []]],
        tags: ['Status do Quadro'],
        parameters: [
            new OA\Parameter(name: 'quadroId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Status apagado com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
            new OA\Response(response: 404, description: 'Status ou Quadro não encontrado'),
            ]
    )]
    public function apagarStatus(
        UsuarioAutenticadoRequisicao $requisicao,
        int $quadroId,
        int $id,
        ApagarStatusQuadroUseCase $casoDeUso,
    ): JsonResponse {
        $casoDeUso->executar($id, $requisicao->usuarioId());

        return response()->json(null, 204);
    }
}
