<?php

namespace Tests\Feature\Repositories;

use App\Models\Tarefa\Quadro as QuadroModelo;
use App\Models\Tarefa\StatusQuadro as StatusQuadroModelo;
use App\Models\Tarefa\Tarefa as TarefaModelo;
use App\Models\Usuario;
use App\Repositories\Eloquent\Tarefa\TarefaEloquentRepository;
use Core\Domain\Tarefa\Entities\Tarefa;
use DateTimeImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TarefaEloquentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private TarefaEloquentRepository $repository;

    private Usuario $usuario;

    private QuadroModelo $quadro;

    private StatusQuadroModelo $status;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(TarefaEloquentRepository::class);
        $this->usuario = Usuario::query()->create([
            'nome' => 'Usuario Teste',
            'email' => 'teste@example.com',
            'senha' => 'senha123',
        ]);
        $this->quadro = QuadroModelo::query()->create([
            'nome' => 'Quadro Teste',
            'usuario_id' => $this->usuario->id,
        ]);
        $this->status = StatusQuadroModelo::query()->create([
            'nome' => 'Pendente',
            'quadro_id' => $this->quadro->id,
        ]);
    }

    public function test_criar_retorna_entidade_com_id_preenchido_e_persiste_no_banco(): void
    {
        $tarefa = new Tarefa(
            id: null,
            nome: 'Tarefa Criada',
            status_id: $this->status->id,
            quadro_id: $this->quadro->id,
            descricao: 'Detalhes',
            data_inicio: new DateTimeImmutable('2026-05-01 09:00:00'),
            data_fim: new DateTimeImmutable('2026-05-02 18:00:00'),
        );

        $resultado = $this->repository->criar($this->quadro->id, $tarefa);

        $this->assertNotNull($resultado->id(), 'O ID retornado pelo repositório não deve ser nulo ao criar tarefa.');
        $this->assertSame('Tarefa Criada', $resultado->nome());
        $this->assertSame($this->status->id, $resultado->statusId());
        $this->assertSame($this->quadro->id, $resultado->quadroId());
        $this->assertSame('Detalhes', $resultado->descricao());

        $this->assertDatabaseHas('tarefas', [
            'id' => $resultado->id(),
            'nome' => 'Tarefa Criada',
            'quadro_id' => $this->quadro->id,
            'status_id' => $this->status->id,
            'descricao' => 'Detalhes',
        ]);
    }

    public function test_atualizar_modifica_dados_no_banco_e_retorna_entidade_atualizada(): void
    {
        $modelo = TarefaModelo::query()->create([
            'nome' => 'Tarefa Antiga',
            'quadro_id' => $this->quadro->id,
            'status_id' => $this->status->id,
            'descricao' => 'Desc Antiga',
        ]);

        $tarefaParaAtualizar = new Tarefa(
            id: $modelo->id,
            nome: 'Tarefa Atualizada',
            status_id: $this->status->id,
            quadro_id: $this->quadro->id,
            descricao: 'Nova Desc',
        );

        $resultado = $this->repository->atualizar($modelo->id, $tarefaParaAtualizar);

        $this->assertSame($modelo->id, $resultado->id());
        $this->assertSame('Tarefa Atualizada', $resultado->nome());
        $this->assertSame('Nova Desc', $resultado->descricao());

        $this->assertDatabaseHas('tarefas', [
            'id' => $modelo->id,
            'nome' => 'Tarefa Atualizada',
            'descricao' => 'Nova Desc',
        ]);
    }

    public function test_apagar_realiza_soft_delete(): void
    {
        $modelo = TarefaModelo::query()->create([
            'nome' => 'Tarefa Para Apagar',
            'quadro_id' => $this->quadro->id,
            'status_id' => $this->status->id,
        ]);

        $this->repository->apagar($modelo->id);

        $this->assertSoftDeleted('tarefas', [
            'id' => $modelo->id,
        ]);
    }

    public function test_listar_por_quadro_id_retorna_tarefas_do_quadro(): void
    {
        $outroQuadro = QuadroModelo::query()->create([
            'nome' => 'Outro Quadro',
            'usuario_id' => $this->usuario->id,
        ]);
        $outroStatus = StatusQuadroModelo::query()->create([
            'nome' => 'Outro Status',
            'quadro_id' => $outroQuadro->id,
        ]);

        TarefaModelo::query()->create([
            'nome' => 'Tarefa Quadro 1',
            'quadro_id' => $this->quadro->id,
            'status_id' => $this->status->id,
        ]);

        TarefaModelo::query()->create([
            'nome' => 'Tarefa Quadro 2',
            'quadro_id' => $outroQuadro->id,
            'status_id' => $outroStatus->id,
        ]);

        $resultado = $this->repository->listarPorQuadroId($this->quadro->id);

        $this->assertIsArray($resultado);
        $dados = $resultado['data'] ?? $resultado;
        $this->assertCount(1, $dados);
    }

    public function test_listar_por_usuario_id_retorna_apenas_tarefas_do_usuario(): void
    {
        $outroUsuario = Usuario::query()->create([
            'nome' => 'Outro Usuario',
            'email' => 'outro@example.com',
            'senha' => 'senha123',
        ]);
        $outroQuadro = QuadroModelo::query()->create([
            'nome' => 'Outro Quadro',
            'usuario_id' => $outroUsuario->id,
        ]);
        $outroStatus = StatusQuadroModelo::query()->create([
            'nome' => 'Outro Status',
            'quadro_id' => $outroQuadro->id,
        ]);

        TarefaModelo::query()->create([
            'nome' => 'Minha Tarefa',
            'quadro_id' => $this->quadro->id,
            'status_id' => $this->status->id,
        ]);

        TarefaModelo::query()->create([
            'nome' => 'Tarefa de Outro',
            'quadro_id' => $outroQuadro->id,
            'status_id' => $outroStatus->id,
        ]);

        $resultado = $this->repository->listarPorUsuarioId($this->usuario->id);

        $this->assertIsArray($resultado);
        $dados = $resultado['data'] ?? $resultado;
        $this->assertCount(1, $dados);
    }

    public function test_pertence_ao_usuario(): void
    {
        $outroUsuario = Usuario::query()->create([
            'nome' => 'Outro Usuario',
            'email' => 'outro@example.com',
            'senha' => 'senha123',
        ]);

        $modelo = TarefaModelo::query()->create([
            'nome' => 'Tarefa Teste',
            'quadro_id' => $this->quadro->id,
            'status_id' => $this->status->id,
        ]);

        $this->assertTrue($this->repository->pertenceAoUsuario($modelo->id, $this->usuario->id));
        $this->assertFalse($this->repository->pertenceAoUsuario($modelo->id, $outroUsuario->id));
        $this->assertFalse($this->repository->pertenceAoUsuario(9999, $this->usuario->id));
    }
}
