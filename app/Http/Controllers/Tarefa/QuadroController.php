<?php

namespace App\Http\Controllers\Tarefa;

use App\Http\Controllers\Controller;
use App\Http\Requisicoes\Tarefa\ConvidarUsuarioQuadroRequisicao;
use App\Http\Requisicoes\Tarefa\ListarQuadrosRequisicao;
use App\Http\Requisicoes\Tarefa\SalvarQuadroRequisicao;
use App\Http\Requisicoes\UsuarioAutenticadoRequisicao;
use Core\Application\Tarefa\DTOs\AtualizarQuadroDTO;
use Core\Application\Tarefa\DTOs\CriarQuadroDTO;
use Core\Application\Tarefa\DTOs\ListarQuadrosDTO;
use Core\Application\Tarefa\UseCases\ApagarQuadroUseCase;
use Core\Application\Tarefa\UseCases\AtualizarQuadroUseCase;
use Core\Application\Tarefa\UseCases\ConvidarUsuarioQuadroUseCase;
use Core\Application\Tarefa\UseCases\CriarQuadroUseCase;
use Core\Application\Tarefa\UseCases\ListarQuadrosPorUsuarioIdUseCase;
use Core\Application\Tarefa\UseCases\ListarUsuariosCompartilhamentoUseCase;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class QuadroController extends Controller
{
    #[OA\Get(
        path: "/quadros",
        summary: "Listar quadros do usuário",
        security: [["bearerAuth" => []]],
        tags: ["Quadros"],
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
            ListarQuadrosDTO::deArray($requisicao->validated(), $requisicao->usuarioId()),
        ));
    }

    #[OA\Post(
        path: "/quadros",
        summary: "Criar novo quadro",
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
        tags: ["Quadros"],
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
            CriarQuadroDTO::deArray($requisicao->validated(), $requisicao->usuarioId()),
        ), 201);
    }

    #[OA\Put(
        path: "/quadros/{id}",
        summary: "Editar quadro existente",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "nome", type: "string", example: "Nome Atualizado"),
                    new OA\Property(property: "descricao", type: "string", example: "Descrição atualizada", nullable: true),
                ]
            )
        ),
        tags: ["Quadros"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
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
            AtualizarQuadroDTO::deArray($requisicao->validated(), $id, $requisicao->usuarioId()),
        ));
    }

    #[OA\Delete(
        path: "/quadros/{id}",
        summary: "Apagar quadro",
        security: [["bearerAuth" => []]],
        tags: ["Quadros"],
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

    #[OA\Post(
        path: "/quadros/{id}/compartilhar",
        summary: "Compartilhar quadro com usuário",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "usuario_id", type: "integer", example: 2),
                ]
            )
        ),
        tags: ["Quadros"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Quadro compartilhado com sucesso"),
            new OA\Response(response: 401, description: "Não autenticado"),
            new OA\Response(response: 404, description: "Quadro não encontrado"),
            new OA\Response(response: 422, description: "Erro de validação")
        ]
    )]
    public function compartilharQuadro(
        ConvidarUsuarioQuadroRequisicao $requisicao,
        int $id,
        ConvidarUsuarioQuadroUseCase $casoDeUso,
    ): JsonResponse {
        $casoDeUso->executar($id, (int) $requisicao->input('usuario_id'), $requisicao->usuarioId());

        return response()->json(['mensagem' => 'Quadro compartilhado com sucesso.'], 200);
    }

    #[OA\Get(
        path: "/quadros/usuarios-compartilhamento",
        summary: "Listar usuários disponíveis para compartilhamento (id, nome e e-mail)",
        security: [["bearerAuth" => []]],
        tags: ["Quadros"],
        parameters: [
            new OA\Parameter(name: "page", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 1)),
            new OA\Parameter(name: "per_page", in: "query", required: false, schema: new OA\Schema(type: "integer", default: 10)),
            new OA\Parameter(name: "search", in: "query", required: false, schema: new OA\Schema(type: "string")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Lista de usuários paginada")
        ]
    )]
    public function listarUsuariosCompartilhamento(
        UsuarioAutenticadoRequisicao $requisicao,
        ListarUsuariosCompartilhamentoUseCase $casoDeUso,
    ): JsonResponse {
        $pagina = (int) $requisicao->input('page', 1);
        $porPagina = (int) $requisicao->input('per_page', 10);
        $busca = $requisicao->input('search');

        return response()->json($casoDeUso->executar($pagina, $porPagina, $busca));
    }

    public function listarUsuariosCompartilhadosQuadro(
        UsuarioAutenticadoRequisicao $requisicao,
        int $id,
    ): JsonResponse {
        $quadro = \App\Models\Tarefa\Quadro::query()->with(['dono:id,nome,email', 'usuarios:id,nome,email'])->findOrFail($id);
        
        $usuarios = collect();
        if ($quadro->dono) {
            $usuarios->push([
                'id' => $quadro->dono->id,
                'nome' => $quadro->dono->nome,
                'email' => $quadro->dono->email,
                'tipo' => 'Dono',
            ]);
        }
        foreach ($quadro->usuarios as $u) {
            $usuarios->push([
                'id' => $u->id,
                'nome' => $u->nome,
                'email' => $u->email,
                'tipo' => 'Compartilhado',
            ]);
        }

        return response()->json($usuarios->unique('id')->values());
    }
}
