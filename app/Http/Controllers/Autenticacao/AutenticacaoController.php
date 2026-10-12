<?php

namespace App\Http\Controllers\Autenticacao;

use App\Http\Controllers\Controller;
use App\Http\Requisicoes\Autenticacao\EntrarRequisicao;
use Core\Application\Usuario\DTOs\EntrarDTO;
use Core\Application\Usuario\UseCases\EntrarUseCase;
use Core\Application\Usuario\UseCases\SairUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AutenticacaoController extends Controller
{
    #[OA\Post(
        path: "/autenticacao/entrar",
        summary: "Autenticar usuário",
        description: "Retorna o token de acesso para o usuário",
        tags: ["Autenticação"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "usuario@example.com"),
                    new OA\Property(property: "senha", type: "string", example: "senha-correta"),
                    new OA\Property(property: "nome_dispositivo", type: "string", example: "meu-computador"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Autenticado com sucesso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "usuario", type: "object", properties: [
                            new OA\Property(property: "id", type: "integer"),
                            new OA\Property(property: "nome", type: "string"),
                            new OA\Property(property: "email", type: "string"),
                        ]),
                        new OA\Property(property: "token", type: "string"),
                        new OA\Property(property: "tipo_token", type: "string", example: "Bearer"),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Credenciais inválidas"),
            new OA\Response(response: 422, description: "Erro de validação")
        ]
    )]
    public function entrar(EntrarRequisicao $requisicao, EntrarUseCase $casoDeUso): JsonResponse
    {
        return response()->json($casoDeUso->executar(
            EntrarDTO::daRequisicao($requisicao->validated()),
        ));
    }

    #[OA\Post(
        path: "/autenticacao/sair",
        summary: "Encerrar sessão",
        description: "Invalida o token de acesso atual",
        tags: ["Autenticação"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 204, description: "Sessão encerrada com sucesso"),
            new OA\Response(response: 401, description: "Não autenticado")
        ]
    )]
    public function sair(Request $requisicao, SairUseCase $casoDeUso): JsonResponse
    {
        $casoDeUso->executar((int) $requisicao->user()->currentAccessToken()->getKey());
        return response()->json(null, 204);
    }
}
