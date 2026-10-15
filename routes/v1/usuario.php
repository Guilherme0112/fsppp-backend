<?php

use App\Http\Controllers\Usuario\PapelController;
use App\Http\Controllers\Usuario\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::prefix('/usuarios')->group(function () {
    Route::get('/', [UsuarioController::class, 'listarUsuarios'])
        ->middleware('permission:usuario.listar');

    Route::post('/', [UsuarioController::class, 'criarUsuario'])
        ->middleware('permission:usuario.criar');

    Route::put('/{id}', [UsuarioController::class, 'atualizarUsuario'])
        ->middleware('permission:usuario.editar');

    Route::delete('/{id}', [UsuarioController::class, 'apagarUsuario'])
        ->middleware('permission:usuario.deletar');
});

Route::get('/papeis', [PapelController::class, 'listarPapeis'])
    ->middleware('permission:usuario.listar');
