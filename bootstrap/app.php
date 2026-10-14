<?php

use App\Http\Middleware\ForcarRespostaJson;
use Core\Domain\SolicitacaoPagamento\Exceptions\SolicitacaoNaoEditavelException;
use Core\Domain\SolicitacaoPagamento\Exceptions\SolicitacaoNaoEncontradaException;
use Core\Domain\SolicitacaoPagamento\Exceptions\TransicaoStatusInvalidaException;
use Core\Domain\Tarefa\Exceptions\RecursoNaoEncontradoException;
use Core\Domain\Usuario\Exceptions\CredenciaisInvalidasException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->prefix('api/v1')
                ->group(base_path('routes/v1/api.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prependToGroup('api', ForcarRespostaJson::class);
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function (Request $request): bool {
            return $request->is('api/*') || $request->expectsJson();
        });

        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['mensagem' => 'Não autenticado.'], 401);
            }
        });

        $exceptions->render(function (CredenciaisInvalidasException $exception) {
            return response()->json(['mensagem' => $exception->getMessage()], 401);
        });

        $exceptions->render(function (RecursoNaoEncontradoException $exception) {
            return response()->json(['mensagem' => $exception->getMessage()], 404);
        });

        $exceptions->render(function (SolicitacaoNaoEncontradaException $exception) {
            return response()->json(['mensagem' => $exception->getMessage()], 404);
        });

        $exceptions->render(function (SolicitacaoNaoEditavelException $exception) {
            return response()->json(['mensagem' => $exception->getMessage()], 409);
        });

        $exceptions->render(function (TransicaoStatusInvalidaException $exception) {
            return response()->json(['mensagem' => $exception->getMessage()], 409);
        });
    })->create();
