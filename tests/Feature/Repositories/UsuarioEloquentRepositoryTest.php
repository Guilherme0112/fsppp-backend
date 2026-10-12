<?php

namespace Tests\Feature\Repositories;

use App\Models\Usuario as UsuarioModelo;
use App\Repositories\Eloquent\Usuario\UsuarioEloquentRepository;
use Core\Domain\Usuario\Entities\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsuarioEloquentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private UsuarioEloquentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(UsuarioEloquentRepository::class);
    }

    // ─── criar ────────────────────────────────────────────────────────────────

    public function test_criar_persiste_usuario_e_retorna_entidade_com_id(): void
    {
        $usuario   = new Usuario(null, 'João Silva', 'joao@example.com', 'senha123');
        $resultado = $this->repository->criar($usuario);

        $this->assertNotNull($resultado->id(), 'ID deve ser preenchido após criar');
        $this->assertIsInt($resultado->id());
        $this->assertSame('João Silva', $resultado->nome());
        $this->assertSame('joao@example.com', $resultado->email());

        $this->assertDatabaseHas('usuarios', [
            'id'    => $resultado->id(),
            'nome'  => 'João Silva',
            'email' => 'joao@example.com',
        ]);
    }

    public function test_criar_hash_da_senha_no_banco(): void
    {
        // O modelo Usuario tem 'senha' => 'hashed' no cast,
        // portanto a senha armazenada nunca deve ser o texto puro.
        $usuario = new Usuario(null, 'Segura', 'segura@example.com', 'texto-puro');
        $this->repository->criar($usuario);

        $registro = UsuarioModelo::query()->where('email', 'segura@example.com')->first();
        $this->assertNotSame('texto-puro', $registro->getRawOriginal('senha'),
            'A senha não deve ser armazenada em texto puro');
    }

    // ─── listar ───────────────────────────────────────────────────────────────

    public function test_listar_retorna_todos_os_usuarios_paginados(): void
    {
        UsuarioModelo::query()->create(['nome' => 'A', 'email' => 'a@a.com', 'senha' => '1234']);
        UsuarioModelo::query()->create(['nome' => 'B', 'email' => 'b@b.com', 'senha' => '1234']);

        $resultado = $this->repository->listar();

        $this->assertIsArray($resultado);
        $dados = $resultado['data'] ?? $resultado;
        $this->assertCount(2, $dados);
    }

    public function test_listar_retorna_array_vazio_quando_nao_ha_usuarios(): void
    {
        $resultado = $this->repository->listar();

        $dados = $resultado['data'] ?? $resultado;
        $this->assertEmpty($dados);
    }

    // ─── atualizar ────────────────────────────────────────────────────────────

    public function test_atualizar_modifica_dados_no_banco_e_retorna_entidade_atualizada(): void
    {
        $modelo = UsuarioModelo::query()->create([
            'nome'  => 'Nome Antigo',
            'email' => 'antigo@example.com',
            'senha' => 'senha',
        ]);

        $entidade  = new Usuario($modelo->id, 'Nome Novo', 'novo@example.com', 'novasenha');
        $resultado = $this->repository->atualizar($modelo->id, $entidade);

        $this->assertSame($modelo->id, $resultado->id());
        $this->assertSame('Nome Novo', $resultado->nome());
        $this->assertSame('novo@example.com', $resultado->email());

        $this->assertDatabaseHas('usuarios', [
            'id'    => $modelo->id,
            'nome'  => 'Nome Novo',
            'email' => 'novo@example.com',
        ]);
    }

    public function test_atualizar_lanca_excecao_se_usuario_nao_existe(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->repository->atualizar(99999, new Usuario(99999, 'X', 'x@x.com', 'pass'));
    }

    // ─── apagar ───────────────────────────────────────────────────────────────

    public function test_apagar_realiza_soft_delete(): void
    {
        $modelo = UsuarioModelo::query()->create([
            'nome'  => 'Para Apagar',
            'email' => 'apagar@example.com',
            'senha' => 'senha',
        ]);

        $this->repository->apagar($modelo->id);

        // Usuário não aparece em consultas normais (soft deleted)
        $this->assertNull(UsuarioModelo::query()->find($modelo->id));
        // Mas ainda existe fisicamente no banco
        $this->assertNotNull(UsuarioModelo::withTrashed()->find($modelo->id));
    }

    public function test_apagar_lanca_excecao_se_usuario_nao_existe(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->repository->apagar(99999);
    }
}
