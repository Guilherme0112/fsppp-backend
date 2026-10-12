<?php

namespace Tests\Feature\Tarefa;

use App\Models\Tarefa\Quadro;
use App\Models\Tarefa\StatusQuadro;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatusQuadroControllerTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $usuario;

    private string $token;

    private Quadro $quadro;

    protected function setUp(): void
    {
        parent::setUp();
        $this->usuario = Usuario::query()->create([
            'nome' => 'Usuario Teste',
            'email' => 'usuario@example.com',
            'senha' => 'senha-correta',
        ]);
        $this->token = $this->usuario->createToken('teste')->plainTextToken;
        $this->quadro = Quadro::query()->create([
            'nome' => 'Quadro Teste',
            'usuario_id' => $this->usuario->id,
        ]);
    }

    public function test_listar_status_exige_autenticacao(): void
    {
        $this->getJson("/api/v1/status-quadro/{$this->quadro->id}")->assertUnauthorized();
    }

    public function test_listar_status_retorna_404_se_quadro_nao_pertence_ao_usuario(): void
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
            ->getJson("/api/v1/status-quadro/{$quadroDeOutro->id}")
            ->assertNotFound();
    }

    public function test_listar_status_com_sucesso(): void
    {
        StatusQuadro::query()->create([
            'nome' => 'A Fazer',
            'quadro_id' => $this->quadro->id,
        ]);

        $resposta = $this->withToken($this->token)
            ->getJson("/api/v1/status-quadro/{$this->quadro->id}")
            ->assertOk();

        $dados = $resposta->json('data');
        $this->assertCount(1, $dados);
        $this->assertSame('A Fazer', $dados[0]['nome']);
    }

    public function test_criar_status_com_sucesso(): void
    {
        $resposta = $this->withToken($this->token)
            ->postJson("/api/v1/status-quadro/{$this->quadro->id}", [
                'nome' => 'Em Progresso',
                'descricao' => 'Tarefas em andamento',
            ])
            ->assertCreated();

        $resposta->assertJsonStructure([
            'id',
            'nome',
            'descricao',
            'quadro_id',
        ]);

        $id = $resposta->json('id');
        $this->assertNotNull($id, 'ID retornado ao criar status não deve ser nulo');
        $this->assertIsInt($id);
        $this->assertSame('Em Progresso', $resposta->json('nome'));
        $this->assertSame($this->quadro->id, $resposta->json('quadro_id'));

        $this->assertDatabaseHas('status_quadro', [
            'id' => $id,
            'nome' => 'Em Progresso',
            'quadro_id' => $this->quadro->id,
        ]);
    }

    public function test_criar_status_retorna_404_se_quadro_pertence_a_outro_usuario(): void
    {
        $outroUsuario = Usuario::query()->create([
            'nome' => 'Outro Usuario',
            'email' => 'outro@example.com',
            'senha' => 'senha-correta',
        ]);
        $quadroDeOutro = Quadro::query()->create([
            'nome' => 'Quadro Alheio',
            'usuario_id' => $outroUsuario->id,
        ]);

        $this->withToken($this->token)
            ->postJson("/api/v1/status-quadro/{$quadroDeOutro->id}", [
                'nome' => 'Novo Status',
            ])
            ->assertNotFound();
    }

    public function test_criar_status_retorna_erro_de_validacao_se_nome_ausente(): void
    {
        $this->withToken($this->token)
            ->postJson("/api/v1/status-quadro/{$this->quadro->id}", [
                'descricao' => 'Sem nome',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nome']);
    }

    public function test_editar_status_com_sucesso(): void
    {
        $status = StatusQuadro::query()->create([
            'nome' => 'Antigo',
            'quadro_id' => $this->quadro->id,
        ]);

        $resposta = $this->withToken($this->token)
            ->putJson("/api/v1/status-quadro/{$this->quadro->id}/{$status->id}", [
                'nome' => 'Atualizado',
                'descricao' => 'Nova Desc',
            ])
            ->assertOk();

        $this->assertSame($status->id, $resposta->json('id'));
        $this->assertSame('Atualizado', $resposta->json('nome'));
        $this->assertSame('Nova Desc', $resposta->json('descricao'));

        $this->assertDatabaseHas('status_quadro', [
            'id' => $status->id,
            'nome' => 'Atualizado',
            'descricao' => 'Nova Desc',
        ]);
    }

    public function test_editar_status_retorna_erro_de_validacao_se_nome_ausente(): void
    {
        $status = StatusQuadro::query()->create([
            'nome' => 'Antigo',
            'quadro_id' => $this->quadro->id,
        ]);

        $this->withToken($this->token)
            ->putJson("/api/v1/status-quadro/{$this->quadro->id}/{$status->id}", [
                'nome' => '',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nome']);
    }

    public function test_editar_status_retorna_404_se_quadro_nao_pertence_ao_usuario(): void
    {
        $status = StatusQuadro::query()->create([
            'nome' => 'Status Meu',
            'quadro_id' => $this->quadro->id,
        ]);

        $outroUsuario = Usuario::query()->create([
            'nome' => 'Outro Usuario',
            'email' => 'outro@example.com',
            'senha' => 'senha-correta',
        ]);
        $quadroDeOutro = Quadro::query()->create([
            'nome' => 'Quadro Alheio',
            'usuario_id' => $outroUsuario->id,
        ]);

        $this->withToken($this->token)
            ->putJson("/api/v1/status-quadro/{$quadroDeOutro->id}/{$status->id}", [
                'nome' => 'Tentativa Quadro Alheio',
            ])
            ->assertNotFound();
    }

    public function test_editar_status_retorna_404_se_status_nao_pertence_ao_usuario(): void
    {
        $outroUsuario = Usuario::query()->create([
            'nome' => 'Outro Usuario',
            'email' => 'outro@example.com',
            'senha' => 'senha-correta',
        ]);
        $outroQuadro = Quadro::query()->create([
            'nome' => 'Outro Quadro',
            'usuario_id' => $outroUsuario->id,
        ]);
        $statusDeOutro = StatusQuadro::query()->create([
            'nome' => 'Status Alheio',
            'quadro_id' => $outroQuadro->id,
        ]);

        $this->withToken($this->token)
            ->putJson("/api/v1/status-quadro/{$outroQuadro->id}/{$statusDeOutro->id}", [
                'nome' => 'Tentativa Indevida',
            ])
            ->assertNotFound();
    }

    public function test_apagar_status_com_sucesso(): void
    {
        $status = StatusQuadro::query()->create([
            'nome' => 'Para Deletar',
            'quadro_id' => $this->quadro->id,
        ]);

        $this->withToken($this->token)
            ->deleteJson("/api/v1/status-quadro/{$this->quadro->id}/{$status->id}")
            ->assertNoContent();

        $this->assertSoftDeleted('status_quadro', [
            'id' => $status->id,
        ]);
    }

    public function test_apagar_status_retorna_404_se_pertence_a_outro_usuario(): void
    {
        $outroUsuario = Usuario::query()->create([
            'nome' => 'Outro Usuario',
            'email' => 'outro@example.com',
            'senha' => 'senha-correta',
        ]);
        $outroQuadro = Quadro::query()->create([
            'nome' => 'Outro Quadro',
            'usuario_id' => $outroUsuario->id,
        ]);
        $statusDeOutro = StatusQuadro::query()->create([
            'nome' => 'Status Alheio',
            'quadro_id' => $outroQuadro->id,
        ]);

        $this->withToken($this->token)
            ->deleteJson("/api/v1/status-quadro/{$outroQuadro->id}/{$statusDeOutro->id}")
            ->assertNotFound();
    }
}
