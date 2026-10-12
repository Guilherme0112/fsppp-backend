<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AutenticacaoTest extends TestCase
{
    use RefreshDatabase;

    public function test_rota_protegida_exige_autenticacao(): void
    {
        $this->get('/api/v1/quadros')
            ->assertUnauthorized()
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_erros_da_api_sao_json_sem_cabecalho_accept(): void
    {
        $this->post('/api/v1/autenticacao/entrar')
            ->assertUnprocessable()
            ->assertHeader('Content-Type', 'application/json');

        $this->get('/api/v1/rota-inexistente')
            ->assertNotFound()
            ->assertHeader('Content-Type', 'application/json');

        $this->patch('/api/v1/autenticacao/entrar')
            ->assertMethodNotAllowed()
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_credenciais_invalidas_sao_rejeitadas(): void
    {
        Usuario::query()->create([
            'nome' => 'Usuário Teste',
            'email' => 'usuario@example.com',
            'senha' => 'senha-correta',
        ]);

        $this->postJson('/api/v1/autenticacao/entrar', [
            'email' => 'usuario@example.com',
            'senha' => 'senha-incorreta',
        ])->assertUnauthorized();
    }

    public function test_usuario_pode_entrar_acessar_rota_protegida_e_sair(): void
    {
        Usuario::query()->create([
            'nome' => 'Usuário Teste',
            'email' => 'usuario@example.com',
            'senha' => 'senha-correta',
        ]);

        $resposta = $this->postJson('/api/v1/autenticacao/entrar', [
            'email' => 'usuario@example.com',
            'senha' => 'senha-correta',
            'nome_dispositivo' => 'phpunit',
        ])->assertOk()->assertJsonStructure([
            'usuario' => ['id', 'nome', 'email'],
            'token',
            'tipo_token',
        ]);

        $token = $resposta->json('token');

        $this->withToken($token)->getJson('/api/v1/quadros')->assertOk()->assertJsonPath('data', []);
        $this->withToken($token)->postJson('/api/v1/autenticacao/sair')->assertNoContent();
        Auth::forgetGuards();
        $this->withToken($token)->getJson('/api/v1/quadros')->assertUnauthorized();
    }
}
