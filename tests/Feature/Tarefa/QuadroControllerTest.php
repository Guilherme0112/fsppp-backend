<?php

namespace Tests\Feature\Tarefa;

use App\Models\Tarefa\Quadro;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuadroControllerTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $usuario;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->usuario = Usuario::query()->create([
            'nome' => 'Usuario Teste',
            'email' => 'usuario@example.com',
            'senha' => 'senha-correta',
        ]);
        $this->token = $this->usuario->createToken('teste')->plainTextToken;
    }

    public function test_listar_quadros_exige_autenticacao(): void
    {
        $this->getJson('/api/v1/quadros')->assertUnauthorized();
    }

    public function test_listar_quadros_retorna_apenas_quadros_do_usuario_autenticado(): void
    {
        $outroUsuario = Usuario::query()->create([
            'nome' => 'Outro Usuario',
            'email' => 'outro@example.com',
            'senha' => 'senha-correta',
        ]);

        Quadro::query()->create([
            'nome' => 'Quadro do Usuario',
            'descricao' => 'Desc 1',
            'usuario_id' => $this->usuario->id,
        ]);

        Quadro::query()->create([
            'nome' => 'Quadro de Outro',
            'descricao' => 'Desc 2',
            'usuario_id' => $outroUsuario->id,
        ]);

        $resposta = $this->withToken($this->token)
            ->getJson('/api/v1/quadros')
            ->assertOk();

        $dados = $resposta->json('data');
        $this->assertCount(1, $dados);
        $this->assertSame('Quadro do Usuario', $dados[0]['nome']);
    }

    public function test_criar_quadro_com_sucesso(): void
    {
        $resposta = $this->withToken($this->token)
            ->postJson('/api/v1/quadros', [
                'nome' => 'Novo Quadro',
                'descricao' => 'Descricao do quadro',
            ])
            ->assertCreated();

        $resposta->assertJsonStructure([
            'id',
            'nome',
            'descricao',
            'usuario_id',
        ]);

        $id = $resposta->json('id');
        $this->assertNotNull($id, 'ID retornado ao criar quadro não deve ser nulo');
        $this->assertIsInt($id);
        $this->assertSame('Novo Quadro', $resposta->json('nome'));
        $this->assertSame($this->usuario->id, $resposta->json('usuario_id'));

        $this->assertDatabaseHas('quadros', [
            'id' => $id,
            'nome' => 'Novo Quadro',
            'usuario_id' => $this->usuario->id,
        ]);
    }

    public function test_criar_quadro_retorna_erro_de_validacao_se_nome_ausente(): void
    {
        $this->withToken($this->token)
            ->postJson('/api/v1/quadros', [
                'descricao' => 'Sem nome',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nome']);
    }

    public function test_criar_quadro_retorna_erro_de_validacao_se_nome_maior_que_255_caracteres(): void
    {
        $this->withToken($this->token)
            ->postJson('/api/v1/quadros', [
                'nome' => str_repeat('a', 256),
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nome']);
    }

    public function test_editar_quadro_com_sucesso(): void
    {
        $quadro = Quadro::query()->create([
            'nome' => 'Quadro Antigo',
            'descricao' => 'Descricao Antiga',
            'usuario_id' => $this->usuario->id,
        ]);

        $resposta = $this->withToken($this->token)
            ->putJson("/api/v1/quadros/{$quadro->id}", [
                'nome' => 'Quadro Editado',
                'descricao' => 'Nova Descricao',
            ])
            ->assertOk();

        $this->assertSame($quadro->id, $resposta->json('id'));
        $this->assertSame('Quadro Editado', $resposta->json('nome'));
        $this->assertSame('Nova Descricao', $resposta->json('descricao'));

        $this->assertDatabaseHas('quadros', [
            'id' => $quadro->id,
            'nome' => 'Quadro Editado',
            'descricao' => 'Nova Descricao',
        ]);
    }

    public function test_editar_quadro_retorna_erro_de_validacao_se_nome_invalido(): void
    {
        $quadro = Quadro::query()->create([
            'nome' => 'Quadro Antigo',
            'usuario_id' => $this->usuario->id,
        ]);

        $this->withToken($this->token)
            ->putJson("/api/v1/quadros/{$quadro->id}", [
                'nome' => '',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nome']);
    }

    public function test_editar_quadro_retorna_404_se_quadro_pertence_a_outro_usuario(): void
    {
        $outroUsuario = Usuario::query()->create([
            'nome' => 'Outro Usuario',
            'email' => 'outro@example.com',
            'senha' => 'senha-correta',
        ]);

        $quadroDeOutro = Quadro::query()->create([
            'nome' => 'Quadro de Outro',
            'usuario_id' => $outroUsuario->id,
        ]);

        $this->withToken($this->token)
            ->putJson("/api/v1/quadros/{$quadroDeOutro->id}", [
                'nome' => 'Tentando alterar',
            ])
            ->assertNotFound();
    }

    public function test_editar_quadro_retorna_404_se_quadro_nao_existe(): void
    {
        $this->withToken($this->token)
            ->putJson('/api/v1/quadros/99999', [
                'nome' => 'Quadro Fantasma',
            ])
            ->assertNotFound();
    }

    public function test_apagar_quadro_com_sucesso(): void
    {
        $quadro = Quadro::query()->create([
            'nome' => 'Quadro Para Apagar',
            'usuario_id' => $this->usuario->id,
        ]);

        $this->withToken($this->token)
            ->deleteJson("/api/v1/quadros/{$quadro->id}")
            ->assertNoContent();

        $this->assertSoftDeleted('quadros', [
            'id' => $quadro->id,
        ]);
    }

    public function test_apagar_quadro_retorna_404_se_pertence_a_outro_usuario(): void
    {
        $outroUsuario = Usuario::query()->create([
            'nome' => 'Outro Usuario',
            'email' => 'outro@example.com',
            'senha' => 'senha-correta',
        ]);

        $quadro = Quadro::query()->create([
            'nome' => 'Quadro De Outro',
            'usuario_id' => $outroUsuario->id,
        ]);

        $this->withToken($this->token)
            ->deleteJson("/api/v1/quadros/{$quadro->id}")
            ->assertNotFound();
    }
}
