<?php

use App\Http\Controllers\SolicitacaoPagamento\SolicitacaoPagamentoController;
use Illuminate\Support\Facades\Route;

Route::prefix('/solicitacoes-pagamento')->group(function () {
    Route::post('/', [SolicitacaoPagamentoController::class, 'criar'])
        ->middleware('permission:solicitacoes.criar');

    Route::get('/minhas', [SolicitacaoPagamentoController::class, 'minhas'])
        ->middleware('permission:solicitacoes.criar');

    Route::get('/todas', [SolicitacaoPagamentoController::class, 'todas'])
        ->middleware('permission:solicitacoes.ver-todas');

    Route::put('/{id}', [SolicitacaoPagamentoController::class, 'atualizar'])
        ->middleware('permission:solicitacoes.criar');

    Route::patch('/{id}/status', [SolicitacaoPagamentoController::class, 'atualizarStatus'])
        ->middleware('permission:solicitacoes.aprovar');

    Route::patch('/{id}/concluir', [SolicitacaoPagamentoController::class, 'concluir'])
        ->middleware('permission:solicitacoes.concluir');
});
