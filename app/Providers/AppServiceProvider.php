<?php

namespace App\Providers;

use App\Repositories\Eloquent\SolicitacaoPagamento\SolicitacaoPagamentoEloquentRepository;
use App\Repositories\Eloquent\Tarefa\QuadroEloquentRepository;
use App\Repositories\Eloquent\Tarefa\StatusQuadroEloquentRepository;
use App\Repositories\Eloquent\Tarefa\TarefaEloquentRepository;
use App\Repositories\Eloquent\Usuario\SanctumAutenticacaoRepository;
use App\Repositories\Eloquent\Usuario\UsuarioEloquentRepository;
use Core\Domain\SolicitacaoPagamento\Repositories\SolicitacaoPagamentoRepository;
use Core\Domain\Tarefa\Repositories\QuadroRepository;
use Core\Domain\Tarefa\Repositories\StatusQuadroRepository;
use Core\Domain\Tarefa\Repositories\TarefaRepository;
use Core\Domain\Usuario\Repositories\AutenticacaoRepository;
use Core\Domain\Usuario\Repositories\UsuarioRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TarefaRepository::class, TarefaEloquentRepository::class);
        $this->app->bind(QuadroRepository::class, QuadroEloquentRepository::class);
        $this->app->bind(StatusQuadroRepository::class, StatusQuadroEloquentRepository::class);
        $this->app->bind(AutenticacaoRepository::class, SanctumAutenticacaoRepository::class);
        $this->app->bind(UsuarioRepository::class, UsuarioEloquentRepository::class);
        $this->app->bind(SolicitacaoPagamentoRepository::class, SolicitacaoPagamentoEloquentRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
