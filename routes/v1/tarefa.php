<?php

use App\Http\Controllers\Tarefa\QuadroController;
use App\Http\Controllers\Tarefa\StatusQuadroController;
use App\Http\Controllers\Tarefa\TarefaController;
use Illuminate\Support\Facades\Route;

Route::prefix('/tarefas')->group(function () {
    Route::get('/', [TarefaController::class, 'listarTarefas']);
    Route::get('/status/{statusId}', [TarefaController::class, 'listarTarefasPorStatus']);
    Route::post('/', [TarefaController::class, 'criarTarefa']);
    Route::put('/{id}', [TarefaController::class, 'atualizarTarefa']);
    Route::delete('/{id}', [TarefaController::class, 'apagarTarefa']);
});

Route::prefix('/status-quadro')->group(function () {
    Route::get('/{quadroId}', [StatusQuadroController::class, 'listarStatusPorQuadro']);
    Route::post('/{quadroId}', [StatusQuadroController::class, 'criarStatus']);
    Route::put('/{quadroId}/{id}', [StatusQuadroController::class, 'editarStatus']);
    Route::delete('/{quadroId}/{id}', [StatusQuadroController::class, 'apagarStatus']);
});

Route::prefix('/quadros')->group(function () {
    Route::get('/', [QuadroController::class, 'listarQuadros']);
    Route::post('/', [QuadroController::class, 'criarQuadro']);
    Route::put('/{id}', [QuadroController::class, 'editarQuadro']);
    Route::delete('/{id}', [QuadroController::class, 'apagarQuadro']);
});
