<?php

namespace Tests\Feature\Usuario;

use App\Models\Usuario;
use Database\Seeders\PermissaoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsuarioControllerTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $usuario;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->usuario = Usuario::query()->create([
            'nome'  => 'Admin Teste',
            'email' => 'admin@example.com',
            'senha' => 'senha-correta',
        ]);
        $this->token = $this->usuario->createToken('teste')->plainTextToken;
    }

    // ─── Autenticação ─────────────────────────────────────────────────────────

    public function test_listar_usuarios_exige_autenticacao(): void
    {
        $this->getJson('/api/v1/usuarios')->assertUnauthorized();
    }

    public function test_criar_usuario_exige_autenticacao(): void
    {
        $this->postJson('/api/v1/usuarios', [])->assertUnauthorized();
    }

    public function test_atualizar_usuario_exige_autenticacao(): void
    {
        $this->putJson('/api/v1/usuarios/1', [])->assertUnauthorized();
    }

    public function test_apagar_usuario_exige_autenticacao(): void
    {
        $this->deleteJson('/api/v1/usuarios/1')->assertUnauthorized();
    }

    // ─── Listar ───────────────────────────────────────────────────────────────

    public function test_listar_usuarios_retorna_200_com_lista_paginada(): void
    {
        Usuario::query()->create(['nome' => 'Outro', 'email' => 'outro@example.com', 'senha' => 'abc']);

        $resposta = $this->withToken($this->token)
            ->getJson('/api/v1/usuarios')
            ->assertOk();

        $this->assertIsArray($resposta->json('data'));
        $this->assertCount(2, $resposta->json('data'));
    }

    public function test_listar_retorna_estrutura_de_usuario_correta(): void
    {
        $resposta = $this->withToken($this->token)
            ->getJson('/api/v1/usuarios')
            ->assertOk();

        $resposta->assertJsonStructure([
            'data' => [
                '*' => ['id', 'nome', 'email'],
            ],
        ]);
    }

    // ─── Criar ────────────────────────────────────────────────────────────────

    public function test_criar_usuario_retorna_201_com_dados_do_novo_usuario(): void
    {
        $resposta = $this->withToken($this->token)
            ->postJson('/api/v1/usuarios', [
                'nome'  => 'Novo Usuário',
                'email' => 'novo@example.com',
                'senha' => 'senha123',
            ])
            ->assertCreated();

        $resposta->assertJsonStructure(['id', 'nome', 'email']);

        $id = $resposta->json('id');
        $this->assertNotNull($id, 'ID retornado não deve ser nulo');
        $this->assertIsInt($id);
        $this->assertSame('Novo Usuário', $resposta->json('nome'));
        $this->assertSame('novo@example.com', $resposta->json('email'));

        $this->assertDatabaseHas('usuarios', [
            'id'    => $id,
            'nome'  => 'Novo Usuário',
            'email' => 'novo@example.com',
        ]);
    }

    public function test_criar_usuario_retorna_422_se_nome_ausente(): void
    {
        $this->withToken($this->token)
            ->postJson('/api/v1/usuarios', [
                'email' => 'sem@nome.com',
                'senha' => 'senha123',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nome']);
    }

    public function test_criar_usuario_retorna_422_se_email_ausente(): void
    {
        $this->withToken($this->token)
            ->postJson('/api/v1/usuarios', [
                'nome'  => 'Sem Email',
                'senha' => 'senha123',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_criar_usuario_retorna_422_se_email_invalido(): void
    {
        $this->withToken($this->token)
            ->postJson('/api/v1/usuarios', [
                'nome'  => 'Email Ruim',
                'email' => 'nao-e-um-email',
                'senha' => 'senha123',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_criar_usuario_retorna_422_se_senha_muito_curta(): void
    {
        $this->withToken($this->token)
            ->postJson('/api/v1/usuarios', [
                'nome'  => 'Senha Curta',
                'email' => 'curta@example.com',
                'senha' => '123',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['senha']);
    }

    public function test_criar_usuario_aceita_sem_senha_campo_opcional(): void
    {
        // senha é nullable na SalvarUsuarioRequisicao — deve passar validação
        $this->withToken($this->token)
            ->postJson('/api/v1/usuarios', [
                'nome'  => 'Sem Senha',
                'email' => 'semsenha@example.com',
            ])
            ->assertCreated();
    }

    // ─── Atualizar ────────────────────────────────────────────────────────────

    public function test_atualizar_usuario_retorna_200_com_dados_atualizados(): void
    {
        $alvo = Usuario::query()->create([
            'nome'  => 'Antigo',
            'email' => 'antigo@example.com',
            'senha' => 'senha',
        ]);

        $resposta = $this->withToken($this->token)
            ->putJson("/api/v1/usuarios/{$alvo->id}", [
                'nome'  => 'Novo Nome',
                'email' => 'novo@example.com',
            ])
            ->assertOk();

        $this->assertSame($alvo->id, $resposta->json('id'));
        $this->assertSame('Novo Nome', $resposta->json('nome'));
        $this->assertSame('novo@example.com', $resposta->json('email'));

        $this->assertDatabaseHas('usuarios', [
            'id'    => $alvo->id,
            'nome'  => 'Novo Nome',
            'email' => 'novo@example.com',
        ]);
    }

    public function test_atualizar_usuario_retorna_404_se_usuario_nao_existe(): void
    {
        $this->withToken($this->token)
            ->putJson('/api/v1/usuarios/99999', [
                'nome'  => 'Ninguem',
                'email' => 'ninguem@example.com',
            ])
            ->assertNotFound();
    }

    public function test_atualizar_usuario_retorna_422_se_nome_ausente(): void
    {
        $this->withToken($this->token)
            ->putJson("/api/v1/usuarios/{$this->usuario->id}", [
                'email' => 'ok@example.com',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nome']);
    }

    public function test_atualizar_usuario_retorna_422_se_email_invalido(): void
    {
        $this->withToken($this->token)
            ->putJson("/api/v1/usuarios/{$this->usuario->id}", [
                'nome'  => 'Ok',
                'email' => 'nao-e-email',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    // ─── Apagar ───────────────────────────────────────────────────────────────

    public function test_apagar_usuario_retorna_204_e_faz_soft_delete(): void
    {
        $alvo = Usuario::query()->create([
            'nome'  => 'Para Apagar',
            'email' => 'apagar@example.com',
            'senha' => 'senha',
        ]);

        $this->withToken($this->token)
            ->deleteJson("/api/v1/usuarios/{$alvo->id}")
            ->assertNoContent();

        $this->assertNull(Usuario::query()->find($alvo->id));
        $this->assertNotNull(Usuario::withTrashed()->find($alvo->id));
    }

    public function test_apagar_usuario_retorna_404_se_usuario_nao_existe(): void
    {
        $this->withToken($this->token)
            ->deleteJson('/api/v1/usuarios/99999')
            ->assertNotFound();
    }

    public function test_resposta_nao_expoe_campo_senha(): void
    {
        $resposta = $this->withToken($this->token)
            ->postJson('/api/v1/usuarios', [
                'nome'  => 'Sem Vazar',
                'email' => 'seguro@example.com',
                'senha' => 'senhaSecreta',
            ])
            ->assertCreated();

        $this->assertArrayNotHasKey('senha', $resposta->json(),
            'A senha nunca deve aparecer na resposta JSON');
    }

    // ─── Papéis ───────────────────────────────────────────────────────────────

    public function test_listar_papeis_retorna_os_papeis_seedados(): void
    {
        $this->seed(PermissaoSeeder::class);

        $resposta = $this->withToken($this->token)->getJson('/api/v1/papeis')->assertOk();

        $this->assertEqualsCanonicalizing(
            ['Admin', 'Quadros e Tarefas', 'Pagamentos', 'Aprovador Pagamentos', 'Usuários'],
            $resposta->json(),
        );
    }

    public function test_criar_usuario_com_papeis_atribui_os_papeis(): void
    {
        $this->seed(PermissaoSeeder::class);

        $resposta = $this->withToken($this->token)
            ->postJson('/api/v1/usuarios', [
                'nome' => 'Com Papeis',
                'email' => 'compapeis@example.com',
                'senha' => 'senha123',
                'papeis' => ['Pagamentos', 'Aprovador Pagamentos'],
            ])
            ->assertCreated();

        $this->assertEqualsCanonicalizing(['Pagamentos', 'Aprovador Pagamentos'], $resposta->json('papeis'));

        $criado = Usuario::query()->where('email', 'compapeis@example.com')->first();
        $this->assertTrue($criado->hasRole('Pagamentos'));
        $this->assertTrue($criado->hasRole('Aprovador Pagamentos'));
    }

    public function test_criar_usuario_com_papel_invalido_retorna_422(): void
    {
        $this->seed(PermissaoSeeder::class);

        $this->withToken($this->token)
            ->postJson('/api/v1/usuarios', [
                'nome' => 'Papel Invalido',
                'email' => 'invalido@example.com',
                'senha' => 'senha123',
                'papeis' => ['Papel Que Não Existe'],
            ])
            ->assertJsonValidationErrors(['papeis.0']);
    }

    public function test_atualizar_usuario_troca_os_papeis(): void
    {
        $this->seed(PermissaoSeeder::class);

        $alvo = Usuario::query()->create(['nome' => 'Alvo', 'email' => 'alvo@example.com', 'senha' => 'senha']);
        $alvo->assignRole('Pagamentos');

        $resposta = $this->withToken($this->token)
            ->putJson("/api/v1/usuarios/{$alvo->id}", [
                'nome' => 'Alvo',
                'email' => 'alvo@example.com',
                'papeis' => ['Admin', 'Aprovador Pagamentos'],
            ])
            ->assertOk();

        $this->assertEqualsCanonicalizing(['Admin', 'Aprovador Pagamentos'], $resposta->json('papeis'));
        $this->assertTrue($alvo->fresh()->hasRole('Admin'));
        $this->assertTrue($alvo->fresh()->hasRole('Aprovador Pagamentos'));
        $this->assertFalse($alvo->fresh()->hasRole('Pagamentos'));
    }

    public function test_atualizar_usuario_sem_papeis_remove_todos_os_papeis_atuais(): void
    {
        $this->seed(PermissaoSeeder::class);

        $alvo = Usuario::query()->create(['nome' => 'Alvo', 'email' => 'alvo2@example.com', 'senha' => 'senha']);
        $alvo->assignRole('Pagamentos');

        $this->withToken($this->token)
            ->putJson("/api/v1/usuarios/{$alvo->id}", [
                'nome' => 'Alvo',
                'email' => 'alvo2@example.com',
            ])
            ->assertOk()
            ->assertJsonPath('papeis', []);

        $this->assertCount(0, $alvo->fresh()->getRoleNames());
    }

    public function test_listar_usuarios_retorna_os_papeis_atuais(): void
    {
        $this->seed(PermissaoSeeder::class);
        $this->usuario->assignRole(['Admin', 'Aprovador Pagamentos']);

        $resposta = $this->withToken($this->token)->getJson('/api/v1/usuarios')->assertOk();

        $usuarioNaLista = collect($resposta->json('data'))->firstWhere('id', $this->usuario->id);
        $this->assertEqualsCanonicalizing(['Admin', 'Aprovador Pagamentos'], $usuarioNaLista['papeis']);
    }
}
