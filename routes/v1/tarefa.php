<?php

use App\Http\Controllers\Tarefa\QuadroController;
use App\Http\Controllers\Tarefa\StatusQuadroController;
use App\Http\Controllers\Tarefa\TarefaController;
use Illuminate\Support\Facades\Route;

Route::prefix('/tarefas')->group(function () {
    Route::get('/', [TarefaController::class, 'listarTarefas'])
        ->middleware('permission:tarefas.listar');

    Route::get('/status/{statusId}', [TarefaController::class, 'listarTarefasPorStatus'])
        ->middleware('permission:tarefas.listar');

    Route::post('/', [TarefaController::class, 'criarTarefa'])
        ->middleware('permission:tarefas.criar');

    Route::put('/{id}', [TarefaController::class, 'atualizarTarefa'])
        ->middleware('permission:tarefas.editar');

    Route::delete('/{id}', [TarefaController::class, 'apagarTarefa'])
        ->middleware('permission:tarefas.deletar');
});

Route::prefix('/status-quadro')->group(function () {
    Route::get('/{quadroId}', [StatusQuadroController::class, 'listarStatusPorQuadro'])
        ->middleware('permission:quadros.listar');

    Route::post('/{quadroId}', [StatusQuadroController::class, 'criarStatus'])
        ->middleware('permission:quadros.editar');

    Route::put('/{quadroId}/{id}', [StatusQuadroController::class, 'editarStatus'])
        ->middleware('permission:quadros.editar');

    Route::delete('/{quadroId}/{id}', [StatusQuadroController::class, 'apagarStatus'])
        ->middleware('permission:quadros.editar');
});

Route::prefix('/quadros')->group(function () {
    Route::get('/', [QuadroController::class, 'listarQuadros'])
        ->middleware('permission:quadros.listar');

    Route::post('/', [QuadroController::class, 'criarQuadro'])
        ->middleware('permission:quadros.criar');

    Route::put('/{id}', [QuadroController::class, 'editarQuadro'])
        ->middleware('permission:quadros.editar');

    Route::delete('/{id}', [QuadroController::class, 'apagarQuadro'])
        ->middleware('permission:quadros.deletar');
});
