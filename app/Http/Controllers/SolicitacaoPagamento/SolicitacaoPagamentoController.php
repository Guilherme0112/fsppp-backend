<?php

namespace App\Http\Controllers\SolicitacaoPagamento;

use App\Http\Controllers\Controller;
use App\Http\Requisicoes\SolicitacaoPagamento\AtualizarStatusSolicitacaoRequisicao;
use App\Http\Requisicoes\SolicitacaoPagamento\ListarSolicitacoesRequisicao;
use App\Http\Requisicoes\SolicitacaoPagamento\ListarTodasSolicitacoesRequisicao;
use App\Http\Requisicoes\SolicitacaoPagamento\SalvarSolicitacaoPagamentoRequisicao;
use App\Http\Requisicoes\UsuarioAutenticadoRequisicao;
use Core\Application\SolicitacaoPagamento\DTOs\AprovarOuCancelarSolicitacaoDTO;
use Core\Application\SolicitacaoPagamento\DTOs\AtualizarSolicitacaoPagamentoDTO;
use Core\Application\SolicitacaoPagamento\DTOs\ConcluirSolicitacaoDTO;
use Core\Application\SolicitacaoPagamento\DTOs\CriarSolicitacaoPagamentoDTO;
use Core\Application\SolicitacaoPagamento\DTOs\ListarSolicitacoesUsuarioDTO;
use Core\Application\SolicitacaoPagamento\DTOs\ListarTodasSolicitacoesDTO;
use Core\Application\SolicitacaoPagamento\UseCases\AprovarOuCancelarSolicitacaoUseCase;
use Core\Application\SolicitacaoPagamento\UseCases\AtualizarSolicitacaoPagamentoUseCase;
use Core\Application\SolicitacaoPagamento\UseCases\ConcluirSolicitacaoUseCase;
use Core\Application\SolicitacaoPagamento\UseCases\CriarSolicitacaoPagamentoUseCase;
use Core\Application\SolicitacaoPagamento\UseCases\ListarSolicitacoesUsuarioUseCase;
use Core\Application\SolicitacaoPagamento\UseCases\ListarTodasSolicitacoesUseCase;
use Illuminate\Http\JsonResponse;

class SolicitacaoPagamentoController extends Controller
{
    public function criar(
        SalvarSolicitacaoPagamentoRequisicao $requisicao,
        CriarSolicitacaoPagamentoUseCase $casoDeUso,
    ): JsonResponse {
        return response()->json($casoDeUso->executar(
            CriarSolicitacaoPagamentoDTO::daRequisicao($requisicao->validated(), $requisicao->usuarioId()),
        ), 201);
    }

    public function atualizar(
        SalvarSolicitacaoPagamentoRequisicao $requisicao,
        int $id,
        AtualizarSolicitacaoPagamentoUseCase $casoDeUso,
    ): JsonResponse {
        return response()->json($casoDeUso->executar(
            AtualizarSolicitacaoPagamentoDTO::daRequisicao($requisicao->validated(), $id, $requisicao->usuarioId()),
        ));
    }

    public function minhas(
        ListarSolicitacoesRequisicao $requisicao,
        ListarSolicitacoesUsuarioUseCase $casoDeUso,
    ): JsonResponse {
        return response()->json($casoDeUso->executar(
            ListarSolicitacoesUsuarioDTO::daRequisicao($requisicao->validated(), $requisicao->usuarioId()),
        ));
    }

    public function todas(
        ListarTodasSolicitacoesRequisicao $requisicao,
        ListarTodasSolicitacoesUseCase $casoDeUso,
    ): JsonResponse {
        return response()->json($casoDeUso->executar(
            ListarTodasSolicitacoesDTO::daRequisicao($requisicao->validated()),
        ));
    }

    public function atualizarStatus(
        AtualizarStatusSolicitacaoRequisicao $requisicao,
        int $id,
        AprovarOuCancelarSolicitacaoUseCase $casoDeUso,
    ): JsonResponse {
        return response()->json($casoDeUso->executar(
            AprovarOuCancelarSolicitacaoDTO::daRequisicao($requisicao->validated(), $id, $requisicao->usuarioId()),
        ));
    }

    public function concluir(
        UsuarioAutenticadoRequisicao $requisicao,
        int $id,
        ConcluirSolicitacaoUseCase $casoDeUso,
    ): JsonResponse {
        return response()->json($casoDeUso->executar(
            new ConcluirSolicitacaoDTO($id, $requisicao->usuarioId()),
        ));
    }
}
