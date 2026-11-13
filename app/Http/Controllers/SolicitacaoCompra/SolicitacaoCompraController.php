<?php

namespace App\Http\Controllers\SolicitacaoCompra;

use App\Http\Controllers\Controller;
use App\Http\Requisicoes\SolicitacaoCompra\AtualizarStatusCompraRequisicao;
use App\Http\Requisicoes\SolicitacaoCompra\AtualizarStatusSolicitacaoRequisicao;
use App\Http\Requisicoes\SolicitacaoCompra\AvaliarDiretoriaRequisicao;
use App\Http\Requisicoes\SolicitacaoCompra\AvaliarProjetosRequisicao;
use App\Http\Requisicoes\SolicitacaoCompra\ListarSolicitacoesRequisicao;
use App\Http\Requisicoes\SolicitacaoCompra\ListarTodasSolicitacoesRequisicao;
use App\Http\Requisicoes\SolicitacaoCompra\SalvarSolicitacaoCompraRequisicao;
use App\Http\Requisicoes\UsuarioAutenticadoRequisicao;
use Core\Application\SolicitacaoCompra\DTOs\AprovarOuCancelarSolicitacaoDTO;
use Core\Application\SolicitacaoCompra\DTOs\AtualizarSolicitacaoCompraDTO;
use Core\Application\SolicitacaoCompra\DTOs\AtualizarStatusCompraDTO;
use Core\Application\SolicitacaoCompra\DTOs\AvaliarDiretoriaDTO;
use Core\Application\SolicitacaoCompra\DTOs\AvaliarProjetosDTO;
use Core\Application\SolicitacaoCompra\DTOs\ConcluirSolicitacaoDTO;
use Core\Application\SolicitacaoCompra\DTOs\CriarSolicitacaoCompraDTO;
use Core\Application\SolicitacaoCompra\DTOs\EnviarParaDiretoriaDTO;
use Core\Application\SolicitacaoCompra\DTOs\ListarSolicitacoesUsuarioDTO;
use Core\Application\SolicitacaoCompra\DTOs\ListarTodasSolicitacoesDTO;
use Core\Application\SolicitacaoCompra\UseCases\AprovarOuCancelarSolicitacaoUseCase;
use Core\Application\SolicitacaoCompra\UseCases\AtualizarSolicitacaoCompraUseCase;
use Core\Application\SolicitacaoCompra\UseCases\AtualizarStatusCompraUseCase;
use Core\Application\SolicitacaoCompra\UseCases\AvaliarDiretoriaUseCase;
use Core\Application\SolicitacaoCompra\UseCases\AvaliarProjetosUseCase;
use Core\Application\SolicitacaoCompra\UseCases\ConcluirSolicitacaoUseCase;
use Core\Application\SolicitacaoCompra\UseCases\CriarSolicitacaoCompraUseCase;
use Core\Application\SolicitacaoCompra\UseCases\EnviarParaDiretoriaUseCase;
use Core\Application\SolicitacaoCompra\UseCases\ListarSolicitacoesUsuarioUseCase;
use Core\Application\SolicitacaoCompra\UseCases\ListarTodasSolicitacoesUseCase;
use Core\Application\SolicitacaoCompra\UseCases\ObterResumoSolicitacoesUseCase;
use Illuminate\Http\JsonResponse;

class SolicitacaoCompraController extends Controller
{
    public function criar(
        SalvarSolicitacaoCompraRequisicao $requisicao,
        CriarSolicitacaoCompraUseCase $casoDeUso,
    ): JsonResponse {
        return response()->json($casoDeUso->executar(
            CriarSolicitacaoCompraDTO::deArray($requisicao->validated(), $requisicao->usuarioId()),
        ), 201);
    }

    public function atualizar(
        SalvarSolicitacaoCompraRequisicao $requisicao,
        int $id,
        AtualizarSolicitacaoCompraUseCase $casoDeUso,
    ): JsonResponse {
        return response()->json($casoDeUso->executar(
            AtualizarSolicitacaoCompraDTO::deArray($requisicao->validated(), $id, $requisicao->usuarioId()),
        ));
    }

    public function enviarParaDiretoria(
        UsuarioAutenticadoRequisicao $requisicao,
        int $id,
        EnviarParaDiretoriaUseCase $casoDeUso,
    ): JsonResponse {
        return response()->json($casoDeUso->executar(
            EnviarParaDiretoriaDTO::deArray([], $id, $requisicao->usuarioId()),
        ));
    }

    public function avaliarDiretoria(
        AvaliarDiretoriaRequisicao $requisicao,
        int $id,
        AvaliarDiretoriaUseCase $casoDeUso,
    ): JsonResponse {
        return response()->json($casoDeUso->executar(
            AvaliarDiretoriaDTO::deArray($requisicao->validated(), $id, $requisicao->usuarioId()),
        ));
    }

    public function avaliarProjetos(
        AvaliarProjetosRequisicao $requisicao,
        int $id,
        AvaliarProjetosUseCase $casoDeUso,
    ): JsonResponse {
        return response()->json($casoDeUso->executar(
            AvaliarProjetosDTO::deArray($requisicao->validated(), $id, $requisicao->usuarioId()),
        ));
    }

    public function atualizarStatusCompra(
        AtualizarStatusCompraRequisicao $requisicao,
        int $id,
        AtualizarStatusCompraUseCase $casoDeUso,
    ): JsonResponse {
        return response()->json($casoDeUso->executar(
            AtualizarStatusCompraDTO::deArray($requisicao->validated(), $id, $requisicao->usuarioId()),
        ));
    }

    public function minhas(
        ListarSolicitacoesRequisicao $requisicao,
        ListarSolicitacoesUsuarioUseCase $casoDeUso,
    ): JsonResponse {
        return response()->json($casoDeUso->executar(
            ListarSolicitacoesUsuarioDTO::deArray($requisicao->validated(), $requisicao->usuarioId()),
        ));
    }

    public function todas(
        ListarTodasSolicitacoesRequisicao $requisicao,
        ListarTodasSolicitacoesUseCase $casoDeUso,
    ): JsonResponse {
        return response()->json($casoDeUso->executar(
            ListarTodasSolicitacoesDTO::deArray($requisicao->validated()),
        ));
    }

    public function resumo(
        UsuarioAutenticadoRequisicao $requisicao,
        ObterResumoSolicitacoesUseCase $casoDeUso,
    ): JsonResponse {
        $usuarioId = $requisicao->user()->can('solicitacoes.ver-todas')
            ? null
            : $requisicao->usuarioId();

        return response()->json($casoDeUso->executar($usuarioId));
    }

    public function atualizarStatus(
        AtualizarStatusSolicitacaoRequisicao $requisicao,
        int $id,
        AprovarOuCancelarSolicitacaoUseCase $casoDeUso,
    ): JsonResponse {
        return response()->json($casoDeUso->executar(
            AprovarOuCancelarSolicitacaoDTO::deArray($requisicao->validated(), $id, $requisicao->usuarioId()),
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
