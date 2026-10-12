<?php

namespace Tests\Feature\Tarefa;

use App\Models\Tarefa\Quadro;
use App\Models\Tarefa\StatusQuadro;
use App\Models\Tarefa\Tarefa;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TarefaControllerTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $usuario;

    private string $token;

    private Quadro $quadro;

    private StatusQuadro $status;

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
        $this->status = StatusQuadro::query()->create([
            'nome' => 'Pendente',
            'quadro_id' => $this->quadro->id,
        ]);
    }

    public function test_listar_tarefas_exige_autenticacao(): void
    {
        $this->getJson('/api/v1/tarefas')->assertUnauthorized();
    }

    public function test_listar_tarefas_retorna_apenas_tarefas_do_usuario_autenticado(): void
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
        $outroStatus = StatusQuadro::query()->create([
            'nome' => 'Status Outro',
            'quadro_id' => $outroQuadro->id,
        ]);

        Tarefa::query()->create([
            'nome' => 'Minha Tarefa',
            'quadro_id' => $this->quadro->id,
            'status_id' => $this->status->id,
        ]);

        Tarefa::query()->create([
            'nome' => 'Tarefa de Outro',
            'quadro_id' => $outroQuadro->id,
            'status_id' => $outroStatus->id,
        ]);

        $resposta = $this->withToken($this->token)
            ->getJson('/api/v1/tarefas')
            ->assertOk();

        $dados = $resposta->json('data');
        $this->assertCount(1, $dados);
        $this->assertSame('Minha Tarefa', $dados[0]['nome']);
    }

    public function test_criar_tarefa_com_sucesso(): void
    {
        $resposta = $this->withToken($this->token)
            ->postJson('/api/v1/tarefas', [
                'nome' => 'Construir Nova Funcionalidade',
                'descricao' => 'Descrição detalhada',
                'quadro_id' => $this->quadro->id,
                'status_id' => $this->status->id,
                'data_inicio' => '2026-06-01',
                'data_fim' => '2026-06-05',
            ])
            ->assertCreated();

        $resposta->assertJsonStructure([
            'id',
            'nome',
            'status_id',
            'quadro_id',
            'descricao',
            'data_inicio',
            'data_fim',
        ]);

        $id = $resposta->json('id');
        $this->assertNotNull($id, 'ID retornado ao criar tarefa não deve ser nulo');
        $this->assertIsInt($id);
        $this->assertSame('Construir Nova Funcionalidade', $resposta->json('nome'));
        $this->assertSame($this->quadro->id, $resposta->json('quadro_id'));
        $this->assertSame($this->status->id, $resposta->json('status_id'));

        $this->assertDatabaseHas('tarefas', [
            'id' => $id,
            'nome' => 'Construir Nova Funcionalidade',
            'quadro_id' => $this->quadro->id,
            'status_id' => $this->status->id,
        ]);
    }

    public function test_criar_tarefa_retorna_404_se_quadro_nao_pertence_ao_usuario(): void
    {
        $outroUsuario = Usuario::query()->create([
            'nome' => 'Outro Usuario',
            'email' => 'outro@example.com',
            'senha' => 'senha-correta',
        ]);
        $outroQuadro = Quadro::query()->create([
            'nome' => 'Quadro Alheio',
            'usuario_id' => $outroUsuario->id,
        ]);
        $outroStatus = StatusQuadro::query()->create([
            'nome' => 'Status Alheio',
            'quadro_id' => $outroQuadro->id,
        ]);

        $this->withToken($this->token)
            ->postJson('/api/v1/tarefas', [
                'nome' => 'Tentativa Indevida',
                'quadro_id' => $outroQuadro->id,
                'status_id' => $outroStatus->id,
            ])
            ->assertNotFound();
    }

    public function test_criar_tarefa_retorna_404_se_status_nao_pertence_ao_quadro(): void
    {
        $outroQuadro = Quadro::query()->create([
            'nome' => 'Segundo Quadro Meu',
            'usuario_id' => $this->usuario->id,
        ]);
        $statusDoOutroQuadro = StatusQuadro::query()->create([
            'nome' => 'Status do Quadro 2',
            'quadro_id' => $outroQuadro->id,
        ]);

        $this->withToken($this->token)
            ->postJson('/api/v1/tarefas', [
                'nome' => 'Status Incompatível',
                'quadro_id' => $this->quadro->id,
                'status_id' => $statusDoOutroQuadro->id,
            ])
            ->assertNotFound();
    }

    public function test_criar_tarefa_retorna_erro_de_validacao_se_campos_obrigatorios_ausentes(): void
    {
        $this->withToken($this->token)
            ->postJson('/api/v1/tarefas', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nome', 'status_id', 'quadro_id']);
    }

    public function test_criar_tarefa_retorna_erro_se_data_fim_anterior_a_data_inicio(): void
    {
        $this->withToken($this->token)
            ->postJson('/api/v1/tarefas', [
                'nome' => 'Datas Invalidas',
                'quadro_id' => $this->quadro->id,
                'status_id' => $this->status->id,
                'data_inicio' => '2026-06-10',
                'data_fim' => '2026-06-05',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['data_fim']);
    }

    public function test_atualizar_tarefa_com_sucesso(): void
    {
        $tarefa = Tarefa::query()->create([
            'nome' => 'Tarefa Antiga',
            'quadro_id' => $this->quadro->id,
            'status_id' => $this->status->id,
        ]);

        $novoStatus = StatusQuadro::query()->create([
            'nome' => 'Concluída',
            'quadro_id' => $this->quadro->id,
        ]);

        $resposta = $this->withToken($this->token)
            ->putJson("/api/v1/tarefas/{$tarefa->id}", [
                'nome' => 'Tarefa Atualizada',
                'descricao' => 'Nova descrição',
                'quadro_id' => $this->quadro->id,
                'status_id' => $novoStatus->id,
            ])
            ->assertOk();

        $this->assertSame($tarefa->id, $resposta->json('id'));
        $this->assertSame('Tarefa Atualizada', $resposta->json('nome'));
        $this->assertSame($novoStatus->id, $resposta->json('status_id'));

        $this->assertDatabaseHas('tarefas', [
            'id' => $tarefa->id,
            'nome' => 'Tarefa Atualizada',
            'status_id' => $novoStatus->id,
        ]);
    }

    public function test_atualizar_tarefa_retorna_404_se_pertence_a_outro_usuario(): void
    {
        $outroUsuario = Usuario::query()->create([
            'nome' => 'Outro Usuario',
            'email' => 'outro@example.com',
            'senha' => 'senha-correta',
        ]);
        $outroQuadro = Quadro::query()->create([
            'nome' => 'Quadro Alheio',
            'usuario_id' => $outroUsuario->id,
        ]);
        $outroStatus = StatusQuadro::query()->create([
            'nome' => 'Status Alheio',
            'quadro_id' => $outroQuadro->id,
        ]);
        $tarefaAlheia = Tarefa::query()->create([
            'nome' => 'Tarefa Alheia',
            'quadro_id' => $outroQuadro->id,
            'status_id' => $outroStatus->id,
        ]);

        $this->withToken($this->token)
            ->putJson("/api/v1/tarefas/{$tarefaAlheia->id}", [
                'nome' => 'Tentativa',
                'quadro_id' => $outroQuadro->id,
                'status_id' => $outroStatus->id,
            ])
            ->assertNotFound();
    }

    public function test_atualizar_tarefa_retorna_404_se_quadro_nao_pertence_ao_usuario(): void
    {
        $tarefa = Tarefa::query()->create([
            'nome' => 'Minha Tarefa',
            'quadro_id' => $this->quadro->id,
            'status_id' => $this->status->id,
        ]);

        $outroUsuario = Usuario::query()->create([
            'nome' => 'Outro Usuario',
            'email' => 'outro@example.com',
            'senha' => 'senha-correta',
        ]);
        $outroQuadro = Quadro::query()->create([
            'nome' => 'Quadro Alheio',
            'usuario_id' => $outroUsuario->id,
        ]);
        $outroStatus = StatusQuadro::query()->create([
            'nome' => 'Status Alheio',
            'quadro_id' => $outroQuadro->id,
        ]);

        $this->withToken($this->token)
            ->putJson("/api/v1/tarefas/{$tarefa->id}", [
                'nome' => 'Tentativa Trocar Quadro',
                'quadro_id' => $outroQuadro->id,
                'status_id' => $outroStatus->id,
            ])
            ->assertNotFound();
    }

    public function test_atualizar_tarefa_retorna_404_se_status_nao_pertence_ao_quadro(): void
    {
        $tarefa = Tarefa::query()->create([
            'nome' => 'Minha Tarefa',
            'quadro_id' => $this->quadro->id,
            'status_id' => $this->status->id,
        ]);

        $outroQuadro = Quadro::query()->create([
            'nome' => 'Segundo Quadro Meu',
            'usuario_id' => $this->usuario->id,
        ]);
        $statusOutroQuadro = StatusQuadro::query()->create([
            'nome' => 'Status Quadro 2',
            'quadro_id' => $outroQuadro->id,
        ]);

        $this->withToken($this->token)
            ->putJson("/api/v1/tarefas/{$tarefa->id}", [
                'nome' => 'Tentativa Status Invalido',
                'quadro_id' => $this->quadro->id,
                'status_id' => $statusOutroQuadro->id,
            ])
            ->assertNotFound();
    }

    public function test_atualizar_tarefa_retorna_erro_se_data_fim_anterior_a_data_inicio(): void
    {
        $tarefa = Tarefa::query()->create([
            'nome' => 'Minha Tarefa',
            'quadro_id' => $this->quadro->id,
            'status_id' => $this->status->id,
        ]);

        $this->withToken($this->token)
            ->putJson("/api/v1/tarefas/{$tarefa->id}", [
                'nome' => 'Datas Invalidas',
                'quadro_id' => $this->quadro->id,
                'status_id' => $this->status->id,
                'data_inicio' => '2026-07-10',
                'data_fim' => '2026-07-05',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['data_fim']);
    }

    public function test_apagar_tarefa_com_sucesso(): void
    {
        $tarefa = Tarefa::query()->create([
            'nome' => 'Tarefa Para Apagar',
            'quadro_id' => $this->quadro->id,
            'status_id' => $this->status->id,
        ]);

        $this->withToken($this->token)
            ->deleteJson("/api/v1/tarefas/{$tarefa->id}")
            ->assertNoContent();

        $this->assertSoftDeleted('tarefas', [
            'id' => $tarefa->id,
        ]);
    }

    public function test_apagar_tarefa_retorna_404_se_pertence_a_outro_usuario(): void
    {
        $outroUsuario = Usuario::query()->create([
            'nome' => 'Outro Usuario',
            'email' => 'outro@example.com',
            'senha' => 'senha-correta',
        ]);
        $outroQuadro = Quadro::query()->create([
            'nome' => 'Quadro Alheio',
            'usuario_id' => $outroUsuario->id,
        ]);
        $outroStatus = StatusQuadro::query()->create([
            'nome' => 'Status Alheio',
            'quadro_id' => $outroQuadro->id,
        ]);
        $tarefaAlheia = Tarefa::query()->create([
            'nome' => 'Tarefa Alheia',
            'quadro_id' => $outroQuadro->id,
            'status_id' => $outroStatus->id,
        ]);

        $this->withToken($this->token)
            ->deleteJson("/api/v1/tarefas/{$tarefaAlheia->id}")
            ->assertNotFound();
    }
}
