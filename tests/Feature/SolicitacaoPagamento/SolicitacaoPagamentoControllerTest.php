<?php

namespace Tests\Feature\SolicitacaoPagamento;

use App\Models\SolicitacaoPagamento\SolicitacaoPagamento;
use App\Models\Usuario;
use Database\Seeders\PermissaoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SolicitacaoPagamentoControllerTest extends TestCase
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
        $this->usuarioComum->assignRole('Pagamentos');
        $this->tokenUsuarioComum = $this->usuarioComum->createToken('teste')->plainTextToken;

        $this->gestor = Usuario::query()->create([
            'nome' => 'Gestor',
            'email' => 'gestor@example.com',
            'senha' => 'senha-correta',
        ]);
        $this->gestor->assignRole('Aprovador Pagamentos');
        $this->tokenGestor = $this->gestor->createToken('teste')->plainTextToken;

        // "Aprovador Pagamentos" now bundles ver-todas + aprovar + concluir into one
        // role, so gestor and financeiro share identical permissions here.
        $this->financeiro = Usuario::query()->create([
            'nome' => 'Financeiro',
            'email' => 'financeiro@example.com',
            'senha' => 'senha-correta',
        ]);
        $this->financeiro->assignRole('Aprovador Pagamentos');
        $this->tokenFinanceiro = $this->financeiro->createToken('teste')->plainTextToken;
    }

    // ─── Criar ────────────────────────────────────────────────────────────────

    public function test_criar_exige_permissao_solicitacoes_criar(): void
    {
        $this->withToken($this->tokenGestor)
            ->postJson('/api/v1/solicitacoes-pagamento', ['titulo' => 'Compra', 'valor' => 1000])
            ->assertForbidden();
    }

    public function test_criar_solicitacao_com_sucesso(): void
    {
        $resposta = $this->withToken($this->tokenUsuarioComum)
            ->postJson('/api/v1/solicitacoes-pagamento', [
                'titulo' => 'Compra de material',
                'valor' => 10000,
                'descricao' => '<p>Detalhes</p>',
            ])
            ->assertCreated();

        $resposta->assertJsonPath('status', 'pendente');
        $resposta->assertJsonPath('usuario_id', $this->usuarioComum->id);

        $this->assertDatabaseHas('solicitacoes_pagamento', [
            'titulo' => 'Compra de material',
            'valor' => 10000,
            'usuario_id' => $this->usuarioComum->id,
            'status' => 'pendente',
        ]);
    }

    // ─── Minhas / Todas ─────────────────────────────────────────────────────────

    public function test_minhas_retorna_apenas_solicitacoes_do_usuario_autenticado(): void
    {
        SolicitacaoPagamento::create(['titulo' => 'Minha', 'valor' => 500, 'usuario_id' => $this->usuarioComum->id]);
        SolicitacaoPagamento::create(['titulo' => 'De outro', 'valor' => 500, 'usuario_id' => $this->gestor->id]);

        $resposta = $this->withToken($this->tokenUsuarioComum)
            ->getJson('/api/v1/solicitacoes-pagamento/minhas')
            ->assertOk();

        $this->assertCount(1, $resposta->json('data'));
        $resposta->assertJsonPath('data.0.titulo', 'Minha');
    }

    public function test_todas_exige_permissao_ver_todas(): void
    {
        $this->withToken($this->tokenUsuarioComum)
            ->getJson('/api/v1/solicitacoes-pagamento/todas')
            ->assertForbidden();
    }

    public function test_todas_retorna_solicitacoes_de_todos_os_usuarios(): void
    {
        SolicitacaoPagamento::create([
            'titulo' => 'A', 'valor' => 500, 'usuario_id' => $this->usuarioComum->id,
            'status' => 'aprovado', 'aprovado_cancelado_por' => $this->gestor->id,
        ]);
        SolicitacaoPagamento::create(['titulo' => 'B', 'valor' => 500, 'usuario_id' => $this->gestor->id]);

        $resposta = $this->withToken($this->tokenGestor)
            ->getJson('/api/v1/solicitacoes-pagamento/todas')
            ->assertOk();

        $dados = $resposta->json('data');
        $this->assertCount(2, $dados);

        $aprovada = collect($dados)->firstWhere('titulo', 'A');
        // The nested relation must not overwrite the raw FK id under the same key.
        $this->assertSame($this->gestor->id, $aprovada['aprovado_cancelado_por']);
        $this->assertSame($this->gestor->id, $aprovada['aprovado_cancelado_por_usuario']['id']);
        $this->assertSame($this->usuarioComum->id, $aprovada['usuario']['id']);
    }

    // ─── Atualizar ────────────────────────────────────────────────────────────

    public function test_dono_pode_atualizar_solicitacao_pendente(): void
    {
        $solicitacao = SolicitacaoPagamento::create(['titulo' => 'Antiga', 'valor' => 500, 'usuario_id' => $this->usuarioComum->id]);

        $this->withToken($this->tokenUsuarioComum)
            ->putJson("/api/v1/solicitacoes-pagamento/{$solicitacao->id}", ['titulo' => 'Nova', 'valor' => 700])
            ->assertOk()
            ->assertJsonPath('titulo', 'Nova');
    }

    public function test_nao_pode_atualizar_solicitacao_ja_aprovada(): void
    {
        $solicitacao = SolicitacaoPagamento::create([
            'titulo' => 'Antiga', 'valor' => 500, 'usuario_id' => $this->usuarioComum->id, 'status' => 'aprovado',
        ]);

        $this->withToken($this->tokenUsuarioComum)
            ->putJson("/api/v1/solicitacoes-pagamento/{$solicitacao->id}", ['titulo' => 'Nova', 'valor' => 700])
            ->assertStatus(409);
    }

    public function test_nao_pode_atualizar_solicitacao_de_outro_usuario(): void
    {
        $solicitacao = SolicitacaoPagamento::create(['titulo' => 'Antiga', 'valor' => 500, 'usuario_id' => $this->gestor->id]);

        $this->withToken($this->tokenUsuarioComum)
            ->putJson("/api/v1/solicitacoes-pagamento/{$solicitacao->id}", ['titulo' => 'Nova', 'valor' => 700])
            ->assertNotFound();
    }

    // ─── Aprovar / Cancelar ─────────────────────────────────────────────────────

    public function test_atualizar_status_exige_permissao_aprovar(): void
    {
        $solicitacao = SolicitacaoPagamento::create(['titulo' => 'A', 'valor' => 500, 'usuario_id' => $this->usuarioComum->id]);

        $this->withToken($this->tokenUsuarioComum)
            ->patchJson("/api/v1/solicitacoes-pagamento/{$solicitacao->id}/status", ['status' => 'aprovado'])
            ->assertForbidden();
    }

    public function test_gestor_pode_aprovar_solicitacao_pendente(): void
    {
        $solicitacao = SolicitacaoPagamento::create(['titulo' => 'A', 'valor' => 500, 'usuario_id' => $this->usuarioComum->id]);

        $this->withToken($this->tokenGestor)
            ->patchJson("/api/v1/solicitacoes-pagamento/{$solicitacao->id}/status", ['status' => 'aprovado'])
            ->assertOk()
            ->assertJsonPath('status', 'aprovado')
            ->assertJsonPath('aprovado_cancelado_por', $this->gestor->id);
    }

    public function test_nao_pode_aprovar_solicitacao_ja_concluida(): void
    {
        $solicitacao = SolicitacaoPagamento::create([
            'titulo' => 'A', 'valor' => 500, 'usuario_id' => $this->usuarioComum->id, 'status' => 'concluido',
        ]);

        $this->withToken($this->tokenGestor)
            ->patchJson("/api/v1/solicitacoes-pagamento/{$solicitacao->id}/status", ['status' => 'aprovado'])
            ->assertStatus(409);
    }

    // ─── Concluir ─────────────────────────────────────────────────────────────

    public function test_concluir_exige_permissao_concluir(): void
    {
        $solicitacao = SolicitacaoPagamento::create([
            'titulo' => 'A', 'valor' => 500, 'usuario_id' => $this->usuarioComum->id, 'status' => 'aprovado',
        ]);

        $this->withToken($this->tokenUsuarioComum)
            ->patchJson("/api/v1/solicitacoes-pagamento/{$solicitacao->id}/concluir")
            ->assertForbidden();
    }

    public function test_financeiro_pode_concluir_solicitacao_aprovada(): void
    {
        $solicitacao = SolicitacaoPagamento::create([
            'titulo' => 'A', 'valor' => 500, 'usuario_id' => $this->usuarioComum->id, 'status' => 'aprovado',
        ]);

        $this->withToken($this->tokenFinanceiro)
            ->patchJson("/api/v1/solicitacoes-pagamento/{$solicitacao->id}/concluir")
            ->assertOk()
            ->assertJsonPath('status', 'concluido')
            ->assertJsonPath('concluido_por', $this->financeiro->id);
    }

    public function test_nao_pode_concluir_solicitacao_pendente(): void
    {
        $solicitacao = SolicitacaoPagamento::create(['titulo' => 'A', 'valor' => 500, 'usuario_id' => $this->usuarioComum->id]);

        $this->withToken($this->tokenFinanceiro)
            ->patchJson("/api/v1/solicitacoes-pagamento/{$solicitacao->id}/concluir")
            ->assertStatus(409);
    }
}
