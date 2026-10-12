<?php

use App\Http\Controllers\Usuario\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::prefix('/usuarios')->group(function () {
    Route::get('/', [UsuarioController::class, 'listarUsuarios']);
    Route::post('/', [UsuarioController::class, 'criarUsuario']);
    Route::put('/{id}', [UsuarioController::class, 'atualizarUsuario']);
    Route::delete('/{id}', [UsuarioController::class, 'apagarUsuario']);
});
