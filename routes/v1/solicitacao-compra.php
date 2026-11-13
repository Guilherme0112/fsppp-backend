<?php

use App\Http\Controllers\SolicitacaoCompra\SolicitacaoCompraController;
use Illuminate\Support\Facades\Route;

Route::prefix('/solicitacoes-compra')->group(function () {
    Route::post('/', [SolicitacaoCompraController::class, 'criar'])
        ->middleware('permission:solicitacoes.criar');

    Route::get('/minhas', [SolicitacaoCompraController::class, 'minhas'])
        ->middleware('permission:solicitacoes.criar');

    Route::get('/todas', [SolicitacaoCompraController::class, 'todas'])
        ->middleware('permission:solicitacoes.ver-todas');

    Route::get('/resumo', [SolicitacaoCompraController::class, 'resumo'])
        ->middleware('permission:solicitacoes.criar|solicitacoes.ver-todas');

    Route::put('/{id}', [SolicitacaoCompraController::class, 'atualizar'])
        ->middleware('permission:solicitacoes.criar');

    Route::patch('/{id}/enviar-diretoria', [SolicitacaoCompraController::class, 'enviarParaDiretoria'])
        ->middleware('permission:solicitacoes.criar');

    Route::patch('/{id}/diretoria', [SolicitacaoCompraController::class, 'avaliarDiretoria'])
        ->middleware('permission:solicitacoes.aprovar');

    Route::patch('/{id}/projetos', [SolicitacaoCompraController::class, 'avaliarProjetos'])
        ->middleware('permission:solicitacoes.aprovar');

    Route::patch('/{id}/status-compra', [SolicitacaoCompraController::class, 'atualizarStatusCompra'])
        ->middleware('permission:solicitacoes.aprovar');

    Route::patch('/{id}/status', [SolicitacaoCompraController::class, 'atualizarStatus'])
        ->middleware('permission:solicitacoes.aprovar');

    Route::patch('/{id}/concluir', [SolicitacaoCompraController::class, 'concluir'])
        ->middleware('permission:solicitacoes.concluir');
});
