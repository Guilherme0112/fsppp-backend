<?php

namespace Tests\Feature\SolicitacaoCompra;

use App\Models\SolicitacaoCompra\SolicitacaoCompra;
use App\Models\Usuario;
use Database\Seeders\PermissaoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SolicitacaoCompraControllerTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $usuarioComum;

    private string $tokenUsuarioComum;

    private Usuario $gestor;

    private string $tokenGestor;

    private Usuario $financeiro;

    private string $tokenFinanceiro;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissaoSeeder::class);

        $this->usuarioComum = Usuario::query()->create([
            'nome' => 'Usuario Comum',
            'email' => 'comum@example.com',
            'senha' => 'senha-correta',
        ]);
        $this->usuarioComum->assignRole('Compras');
        $this->tokenUsuarioComum = $this->usuarioComum->createToken('teste')->plainTextToken;

        $this->gestor = Usuario::query()->create([
            'nome' => 'Gestor',
            'email' => 'gestor@example.com',
            'senha' => 'senha-correta',
        ]);
        $this->gestor->assignRole('Aprovador Compras');
        $this->tokenGestor = $this->gestor->createToken('teste')->plainTextToken;

        $this->financeiro = Usuario::query()->create([
            'nome' => 'Financeiro',
            'email' => 'financeiro@example.com',
            'senha' => 'senha-correta',
        ]);
        $this->financeiro->assignRole('Aprovador Compras');
        $this->tokenFinanceiro = $this->financeiro->createToken('teste')->plainTextToken;
    }

    // ─── Criar ────────────────────────────────────────────────────────────────

    public function test_criar_exige_permissao_solicitacoes_criar(): void
    {
        $this->withToken($this->tokenGestor)
            ->postJson('/api/v1/solicitacoes-compra', ['titulo' => 'Compra', 'valor' => 1000])
            ->assertForbidden();
    }

    public function test_criar_solicitacao_com_sucesso(): void
    {
        $resposta = $this->withToken($this->tokenUsuarioComum)
            ->postJson('/api/v1/solicitacoes-compra', [
                'titulo' => 'Compra de material',
                'valor' => 10000,
                'descricao' => '<p>Detalhes</p>',
            ])
            ->assertCreated();

        $resposta->assertJsonPath('status', 'pendente');
        $resposta->assertJsonPath('usuario_id', $this->usuarioComum->id);

        $this->assertDatabaseHas('solicitacoes_compra', [
            'titulo' => 'Compra de material',
            'valor' => 10000,
            'usuario_id' => $this->usuarioComum->id,
            'status' => 'pendente',
        ]);
    }

    // ─── Minhas / Todas ─────────────────────────────────────────────────────────

    public function test_minhas_retorna_apenas_solicitacoes_do_usuario_autenticado(): void
    {
        SolicitacaoCompra::create(['titulo' => 'Minha', 'valor' => 500, 'usuario_id' => $this->usuarioComum->id]);
        SolicitacaoCompra::create(['titulo' => 'De outro', 'valor' => 500, 'usuario_id' => $this->gestor->id]);

        $resposta = $this->withToken($this->tokenUsuarioComum)
            ->getJson('/api/v1/solicitacoes-compra/minhas')
            ->assertOk();

        $this->assertCount(1, $resposta->json('data'));
        $resposta->assertJsonPath('data.0.titulo', 'Minha');
    }

    public function test_todas_exige_permissao_ver_todas(): void
    {
        $this->withToken($this->tokenUsuarioComum)
            ->getJson('/api/v1/solicitacoes-compra/todas')
            ->assertForbidden();
    }

    public function test_todas_retorna_solicitacoes_de_todos_os_usuarios(): void
    {
        SolicitacaoCompra::create([
            'titulo' => 'A', 'valor' => 500, 'usuario_id' => $this->usuarioComum->id,
            'status' => 'aprovado', 'aprovado_cancelado_por' => $this->gestor->id,
        ]);
        SolicitacaoCompra::create(['titulo' => 'B', 'valor' => 500, 'usuario_id' => $this->gestor->id]);

        $resposta = $this->withToken($this->tokenGestor)
            ->getJson('/api/v1/solicitacoes-compra/todas')
            ->assertOk();

        $dados = $resposta->json('data');
        $this->assertCount(2, $dados);

        $aprovada = collect($dados)->firstWhere('titulo', 'A');
        $this->assertSame($this->gestor->id, $aprovada['aprovado_cancelado_por']);
        $this->assertSame($this->gestor->id, $aprovada['aprovado_cancelado_por_usuario']['id']);
        $this->assertSame($this->usuarioComum->id, $aprovada['usuario']['id']);
    }

    public function test_minhas_filtra_por_busca_e_status(): void
    {
        SolicitacaoCompra::create([
            'titulo' => 'Cadeiras ergonômicas', 'valor' => 500,
            'usuario_id' => $this->usuarioComum->id, 'status' => 'pendente',
        ]);
        SolicitacaoCompra::create([
            'titulo' => 'Notebooks', 'valor' => 500,
            'usuario_id' => $this->usuarioComum->id, 'status' => 'pendente',
        ]);
        SolicitacaoCompra::create([
            'titulo' => 'Cadeiras aprovadas', 'valor' => 500,
            'usuario_id' => $this->usuarioComum->id, 'status' => 'aprovado',
        ]);

        $resposta = $this->withToken($this->tokenUsuarioComum)
            ->getJson('/api/v1/solicitacoes-compra/minhas?busca=cadeiras&status=pendente')
            ->assertOk();

        $this->assertCount(1, $resposta->json('data'));
        $resposta->assertJsonPath('data.0.titulo', 'Cadeiras ergonômicas');
    }

    public function test_resumo_considera_apenas_solicitacoes_do_usuario_sem_permissao_ver_todas(): void
    {
        SolicitacaoCompra::create(['titulo' => 'Pendente', 'valor' => 500, 'usuario_id' => $this->usuarioComum->id]);
        SolicitacaoCompra::create([
            'titulo' => 'Concluída', 'valor' => 500,
            'usuario_id' => $this->usuarioComum->id, 'status' => 'concluido',
        ]);
        SolicitacaoCompra::create([
            'titulo' => 'De outro', 'valor' => 500,
            'usuario_id' => $this->gestor->id, 'status' => 'cancelado',
        ]);

        $this->withToken($this->tokenUsuarioComum)
            ->getJson('/api/v1/solicitacoes-compra/resumo')
            ->assertOk()
            ->assertJsonPath('total', 2)
            ->assertJsonPath('por_status.pendente', 1)
            ->assertJsonPath('por_status.concluido', 1);
    }

    public function test_resumo_considera_todas_as_solicitacoes_com_permissao_ver_todas(): void
    {
        SolicitacaoCompra::create(['titulo' => 'Pendente', 'valor' => 500, 'usuario_id' => $this->usuarioComum->id]);
        SolicitacaoCompra::create([
            'titulo' => 'Concluída', 'valor' => 500,
            'usuario_id' => $this->usuarioComum->id, 'status' => 'concluido',
        ]);
        SolicitacaoCompra::create([
            'titulo' => 'De outro', 'valor' => 500,
            'usuario_id' => $this->gestor->id, 'status' => 'cancelado',
        ]);

        $this->withToken($this->tokenGestor)
            ->getJson('/api/v1/solicitacoes-compra/resumo')
            ->assertOk()
            ->assertJsonPath('total', 3)
            ->assertJsonPath('por_status.pendente', 1)
            ->assertJsonPath('por_status.concluido', 1)
            ->assertJsonPath('por_status.cancelado', 1);
    }

    // ─── Atualizar ────────────────────────────────────────────────────────────

    public function test_dono_pode_atualizar_solicitacao_pendente(): void
    {
        $solicitacao = SolicitacaoCompra::create(['titulo' => 'Antiga', 'valor' => 500, 'usuario_id' => $this->usuarioComum->id]);

        $this->withToken($this->tokenUsuarioComum)
            ->putJson("/api/v1/solicitacoes-compra/{$solicitacao->id}", ['titulo' => 'Nova', 'valor' => 700])
            ->assertOk()
            ->assertJsonPath('titulo', 'Nova');
    }

    public function test_nao_pode_atualizar_solicitacao_ja_aprovada(): void
    {
        $solicitacao = SolicitacaoCompra::create([
            'titulo' => 'Antiga', 'valor' => 500, 'usuario_id' => $this->usuarioComum->id, 'status' => 'aprovado',
        ]);

        $this->withToken($this->tokenUsuarioComum)
            ->putJson("/api/v1/solicitacoes-compra/{$solicitacao->id}", ['titulo' => 'Nova', 'valor' => 700])
            ->assertStatus(409);
    }

    public function test_nao_pode_atualizar_solicitacao_de_outro_usuario(): void
    {
        $solicitacao = SolicitacaoCompra::create(['titulo' => 'Antiga', 'valor' => 500, 'usuario_id' => $this->gestor->id]);

        $this->withToken($this->tokenUsuarioComum)
            ->putJson("/api/v1/solicitacoes-compra/{$solicitacao->id}", ['titulo' => 'Nova', 'valor' => 700])
            ->assertNotFound();
    }

    // ─── Aprovar / Cancelar ─────────────────────────────────────────────────────

    public function test_atualizar_status_exige_permissao_aprovar(): void
    {
        $solicitacao = SolicitacaoCompra::create(['titulo' => 'A', 'valor' => 500, 'usuario_id' => $this->usuarioComum->id]);

        $this->withToken($this->tokenUsuarioComum)
            ->patchJson("/api/v1/solicitacoes-compra/{$solicitacao->id}/status", ['status' => 'aprovado'])
            ->assertForbidden();
    }

    public function test_gestor_pode_aprovar_solicitacao_pendente(): void
    {
        $solicitacao = SolicitacaoCompra::create(['titulo' => 'A', 'valor' => 500, 'usuario_id' => $this->usuarioComum->id]);

        $this->withToken($this->tokenGestor)
            ->patchJson("/api/v1/solicitacoes-compra/{$solicitacao->id}/status", ['status' => 'aprovado'])
            ->assertOk()
            ->assertJsonPath('status', 'aprovado')
            ->assertJsonPath('aprovado_cancelado_por', $this->gestor->id);
    }

    public function test_gestor_pode_avaliar_solicitacao_pendente_na_diretoria(): void
    {
        $solicitacao = SolicitacaoCompra::create(['titulo' => 'A', 'valor' => 500, 'usuario_id' => $this->usuarioComum->id]);

        $this->withToken($this->tokenGestor)
            ->patchJson("/api/v1/solicitacoes-compra/{$solicitacao->id}/diretoria", [
                'aprovado' => true,
                'prioridade' => 'alta',
            ])
            ->assertOk()
            ->assertJsonPath('status', 'analise_projetos')
            ->assertJsonPath('diretoria_por', $this->gestor->id);
    }

    public function test_nao_pode_aprovar_solicitacao_ja_concluida(): void
    {
        $solicitacao = SolicitacaoCompra::create([
            'titulo' => 'A', 'valor' => 500, 'usuario_id' => $this->usuarioComum->id, 'status' => 'concluido',
        ]);

        $this->withToken($this->tokenGestor)
            ->patchJson("/api/v1/solicitacoes-compra/{$solicitacao->id}/status", ['status' => 'aprovado'])
            ->assertStatus(409);
    }

    // ─── Concluir ─────────────────────────────────────────────────────────────

    public function test_concluir_exige_permissao_concluir(): void
    {
        $solicitacao = SolicitacaoCompra::create([
            'titulo' => 'A', 'valor' => 500, 'usuario_id' => $this->usuarioComum->id, 'status' => 'aprovado',
        ]);

        $this->withToken($this->tokenUsuarioComum)
            ->patchJson("/api/v1/solicitacoes-compra/{$solicitacao->id}/concluir")
            ->assertForbidden();
    }

    public function test_financeiro_pode_concluir_solicitacao_aprovada(): void
    {
        $solicitacao = SolicitacaoCompra::create([
            'titulo' => 'A', 'valor' => 500, 'usuario_id' => $this->usuarioComum->id, 'status' => 'aprovado',
        ]);

        $this->withToken($this->tokenFinanceiro)
            ->patchJson("/api/v1/solicitacoes-compra/{$solicitacao->id}/concluir")
            ->assertOk()
            ->assertJsonPath('status', 'concluido')
            ->assertJsonPath('concluido_por', $this->financeiro->id);
    }

    public function test_nao_pode_concluir_solicitacao_pendente(): void
    {
        $solicitacao = SolicitacaoCompra::create(['titulo' => 'A', 'valor' => 500, 'usuario_id' => $this->usuarioComum->id]);

        $this->withToken($this->tokenFinanceiro)
            ->patchJson("/api/v1/solicitacoes-compra/{$solicitacao->id}/concluir")
            ->assertStatus(409);
    }
}
