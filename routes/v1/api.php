<?php

use App\Http\Controllers\Autenticacao\AutenticacaoController;
use Illuminate\Support\Facades\Route;

Route::post('/autenticacao/entrar', [AutenticacaoController::class, 'entrar']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/autenticacao/sair', [AutenticacaoController::class, 'sair']);
    require base_path('routes/v1/tarefa.php');
    require base_path('routes/v1/usuario.php');
    require base_path('routes/v1/solicitacao-pagamento.php');
});
