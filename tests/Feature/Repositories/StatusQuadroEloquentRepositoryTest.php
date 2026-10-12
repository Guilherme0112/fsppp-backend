<?php

namespace Tests\Feature\Repositories;

use App\Models\Tarefa\Quadro as QuadroModelo;
use App\Models\Tarefa\StatusQuadro as StatusQuadroModelo;
use App\Models\Usuario;
use App\Repositories\Eloquent\Tarefa\StatusQuadroEloquentRepository;
use Core\Domain\Tarefa\Entities\StatusQuadro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatusQuadroEloquentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private StatusQuadroEloquentRepository $repository;

    private Usuario $usuario;

    private QuadroModelo $quadro;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(StatusQuadroEloquentRepository::class);
        $this->usuario = Usuario::query()->create([
            'nome' => 'Usuario Teste',
            'email' => 'teste@example.com',
            'senha' => 'senha123',
        ]);
        $this->quadro = QuadroModelo::query()->create([
            'nome' => 'Quadro Teste',
            'usuario_id' => $this->usuario->id,
        ]);
    }

    public function test_criar_retorna_entidade_com_id_preenchido_e_persiste_no_banco(): void
    {
        $status = new StatusQuadro(null, 'A Fazer', $this->quadro->id, 'Descricao Status');

        $resultado = $this->repository->criar($status);

        $this->assertNotNull($resultado->id(), 'O ID retornado pelo repositório não deve ser nulo ao criar status.');
        $this->assertSame('A Fazer', $resultado->nome());
        $this->assertSame($this->quadro->id, $resultado->quadroId());
        $this->assertSame('Descricao Status', $resultado->descricao());

        $this->assertDatabaseHas('status_quadro', [
            'id' => $resultado->id(),
            'nome' => 'A Fazer',
            'quadro_id' => $this->quadro->id,
            'descricao' => 'Descricao Status',
        ]);
    }

    public function test_atualizar_modifica_dados_no_banco_e_retorna_entidade_atualizada(): void
    {
        $modelo = StatusQuadroModelo::query()->create([
            'nome' => 'Status Antigo',
            'quadro_id' => $this->quadro->id,
            'descricao' => 'Descricao Antiga',
        ]);

        $statusParaAtualizar = new StatusQuadro($modelo->id, 'Status Atualizado', $this->quadro->id, 'Nova Descricao');
        $resultado = $this->repository->atualizar($modelo->id, $statusParaAtualizar);

        $this->assertSame($modelo->id, $resultado->id());
        $this->assertSame('Status Atualizado', $resultado->nome());
        $this->assertSame('Nova Descricao', $resultado->descricao());

        $this->assertDatabaseHas('status_quadro', [
            'id' => $modelo->id,
            'nome' => 'Status Atualizado',
            'descricao' => 'Nova Descricao',
        ]);
    }

    public function test_apagar_realiza_soft_delete(): void
    {
        $modelo = StatusQuadroModelo::query()->create([
            'nome' => 'Status Para Apagar',
            'quadro_id' => $this->quadro->id,
        ]);

        $this->repository->apagar($modelo->id);

        $this->assertSoftDeleted('status_quadro', [
            'id' => $modelo->id,
        ]);
    }

    public function test_listar_por_quadro_id_retorna_apenas_status_do_quadro(): void
    {
        $outroQuadro = QuadroModelo::query()->create([
            'nome' => 'Outro Quadro',
            'usuario_id' => $this->usuario->id,
        ]);

        StatusQuadroModelo::query()->create([
            'nome' => 'Status Quadro 1',
            'quadro_id' => $this->quadro->id,
        ]);

        StatusQuadroModelo::query()->create([
            'nome' => 'Status Quadro 2',
            'quadro_id' => $outroQuadro->id,
        ]);

        $resultado = $this->repository->listarPorQuadroId($this->quadro->id);

        $this->assertIsArray($resultado);
        $dados = $resultado['data'] ?? $resultado;
        $this->assertCount(1, $dados);
    }

    public function test_pertence_ao_usuario_e_ao_quadro(): void
    {
        $outroUsuario = Usuario::query()->create([
            'nome' => 'Outro Usuario',
            'email' => 'outro@example.com',
            'senha' => 'senha123',
        ]);

        $modelo = StatusQuadroModelo::query()->create([
            'nome' => 'Status Teste',
            'quadro_id' => $this->quadro->id,
        ]);

        $this->assertTrue($this->repository->pertenceAoUsuario($modelo->id, $this->usuario->id));
        $this->assertFalse($this->repository->pertenceAoUsuario($modelo->id, $outroUsuario->id));

        $this->assertTrue($this->repository->pertenceAoQuadro($modelo->id, $this->quadro->id));
        $this->assertFalse($this->repository->pertenceAoQuadro($modelo->id, 9999));
    }
}
