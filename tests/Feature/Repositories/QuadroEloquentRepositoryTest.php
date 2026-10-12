<?php

namespace Tests\Feature\Repositories;

use App\Models\Tarefa\Quadro as QuadroModelo;
use App\Models\Usuario;
use App\Repositories\Eloquent\Tarefa\QuadroEloquentRepository;
use Core\Domain\Tarefa\Entities\Quadro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuadroEloquentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private QuadroEloquentRepository $repository;

    private Usuario $usuario;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(QuadroEloquentRepository::class);
        $this->usuario = Usuario::query()->create([
            'nome' => 'Usuario Teste',
            'email' => 'teste@example.com',
            'senha' => 'senha123',
        ]);
    }

    public function test_criar_retorna_entidade_com_id_preenchido_e_persiste_no_banco(): void
    {
        $quadro = new Quadro(null, 'Quadro Teste', $this->usuario->id, 'Descricao');

        $resultado = $this->repository->criar($quadro);

        $this->assertNotNull($resultado->id(), 'O ID retornado pelo repositório não deve ser nulo ao criar quadro.');
        $this->assertSame('Quadro Teste', $resultado->nome());
        $this->assertSame($this->usuario->id, $resultado->usuarioId());
        $this->assertSame('Descricao', $resultado->descricao());

        $this->assertDatabaseHas('quadros', [
            'id' => $resultado->id(),
            'nome' => 'Quadro Teste',
            'usuario_id' => $this->usuario->id,
            'descricao' => 'Descricao',
        ]);
    }

    public function test_atualizar_modifica_dados_no_banco_e_retorna_entidade_atualizada(): void
    {
        $modelo = QuadroModelo::query()->create([
            'nome' => 'Quadro Antigo',
            'usuario_id' => $this->usuario->id,
            'descricao' => 'Descricao Antiga',
        ]);

        $quadroParaAtualizar = new Quadro($modelo->id, 'Quadro Novo', $this->usuario->id, 'Descricao Nova');
        $resultado = $this->repository->atualizar($modelo->id, $quadroParaAtualizar);

        $this->assertSame($modelo->id, $resultado->id());
        $this->assertSame('Quadro Novo', $resultado->nome());
        $this->assertSame('Descricao Nova', $resultado->descricao());

        $this->assertDatabaseHas('quadros', [
            'id' => $modelo->id,
            'nome' => 'Quadro Novo',
            'descricao' => 'Descricao Nova',
        ]);
    }

    public function test_apagar_realiza_soft_delete(): void
    {
        $modelo = QuadroModelo::query()->create([
            'nome' => 'Quadro Para Apagar',
            'usuario_id' => $this->usuario->id,
        ]);

        $this->repository->apagar($modelo->id);

        $this->assertSoftDeleted('quadros', [
            'id' => $modelo->id,
        ]);
    }

    public function test_listar_por_usuario_id_retorna_apenas_quadros_do_usuario(): void
    {
        $outroUsuario = Usuario::query()->create([
            'nome' => 'Outro Usuario',
            'email' => 'outro@example.com',
            'senha' => 'senha123',
        ]);

        QuadroModelo::query()->create([
            'nome' => 'Quadro Meu',
            'usuario_id' => $this->usuario->id,
        ]);

        QuadroModelo::query()->create([
            'nome' => 'Quadro Alheio',
            'usuario_id' => $outroUsuario->id,
        ]);

        $resultado = $this->repository->listarPorUsuarioId($this->usuario->id);

        $this->assertIsArray($resultado);
        $dados = $resultado['data'] ?? $resultado;
        $this->assertCount(1, $dados);
    }

    public function test_pertence_ao_usuario(): void
    {
        $modelo = QuadroModelo::query()->create([
            'nome' => 'Quadro Pertence',
            'usuario_id' => $this->usuario->id,
        ]);

        $this->assertTrue($this->repository->pertenceAoUsuario($modelo->id, $this->usuario->id));
        $this->assertFalse($this->repository->pertenceAoUsuario($modelo->id, 9999));
        $this->assertFalse($this->repository->pertenceAoUsuario(9999, $this->usuario->id));
    }
}
