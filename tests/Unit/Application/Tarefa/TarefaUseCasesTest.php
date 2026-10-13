<?php

namespace Tests\Unit\Application\Tarefa;

use Core\Application\Tarefa\DTOs\AtualizarQuadroDTO;
use Core\Application\Tarefa\DTOs\AtualizarStatusQuadroDTO;
use Core\Application\Tarefa\DTOs\AtualizarTarefaDTO;
use Core\Application\Tarefa\DTOs\CriarQuadroDTO;
use Core\Application\Tarefa\DTOs\CriarStatusQuadroDTO;
use Core\Application\Tarefa\DTOs\CriarTarefaDTO;
use Core\Application\Tarefa\DTOs\ListarQuadrosDTO;
use Core\Application\Tarefa\DTOs\ListarTarefasDTO;
use Core\Application\Tarefa\DTOs\ListarTarefasPorStatusDTO;
use Core\Application\Tarefa\UseCases\ApagarQuadroUseCase;
use Core\Application\Tarefa\UseCases\ApagarStatusQuadroUseCase;
use Core\Application\Tarefa\UseCases\ApagarTarefaUseCase;
use Core\Application\Tarefa\UseCases\AtualizarQuadroUseCase;
use Core\Application\Tarefa\UseCases\AtualizarStatusQuadroUseCase;
use Core\Application\Tarefa\UseCases\AtualizarTarefaUseCase;
use Core\Application\Tarefa\UseCases\CriarQuadroUseCase;
use Core\Application\Tarefa\UseCases\CriarStatusQuadroUseCase;
use Core\Application\Tarefa\UseCases\CriarTarefaUseCase;
use Core\Application\Tarefa\UseCases\ListarQuadrosPorUsuarioIdUseCase;
use Core\Application\Tarefa\UseCases\ListarStatusQuadroUseCase;
use Core\Application\Tarefa\UseCases\ListarTarefasPorStatusUseCase;
use Core\Application\Tarefa\UseCases\ListarTarefasUseCase;
use Core\Domain\Tarefa\Entities\Quadro;
use Core\Domain\Tarefa\Entities\StatusQuadro;
use Core\Domain\Tarefa\Entities\Tarefa;
use Core\Domain\Tarefa\Repositories\QuadroRepository;
use Core\Domain\Tarefa\Repositories\StatusQuadroRepository;
use Core\Domain\Tarefa\Repositories\TarefaRepository;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class TarefaUseCasesTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_lista_tarefas_agrupadas_por_status_do_quadro(): void
    {
        $colunas = [['status' => new StatusQuadro(1, 'Pendente', 20), 'tarefas' => [$this->tarefa()]]];
        $repository = Mockery::mock(TarefaRepository::class);
        $repository->shouldReceive('listarAgrupadoPorStatusDoQuadro')->once()->with(20)->andReturn($colunas);
        $quadroRepository = Mockery::mock(QuadroRepository::class);
        $quadroRepository->shouldReceive('pertenceAoUsuario')->once()->with(20, 10)->andReturnTrue();

        $resultado = (new ListarTarefasUseCase($repository, $quadroRepository))->executar(new ListarTarefasDTO(10, 20));

        $this->assertSame($colunas, $resultado);
    }

    public function test_lista_quadros_por_usuario(): void
    {
        $quadros = [$this->quadro()];
        $repository = Mockery::mock(QuadroRepository::class);
        $repository->shouldReceive('listarPorUsuarioId')->once()->with(10)->andReturn($quadros);

        $resultado = (new ListarQuadrosPorUsuarioIdUseCase($repository))->executar(new ListarQuadrosDTO(10));

        $this->assertSame($quadros, $resultado);
    }

    public function test_cria_tarefa(): void
    {
        $tarefa = $this->tarefa();
        $repository = Mockery::mock(TarefaRepository::class);
        $repository->shouldReceive('criar')->once()->with(20, Mockery::type(Tarefa::class))->andReturn($tarefa);

        $quadroRepository = Mockery::mock(QuadroRepository::class);
        $quadroRepository->shouldReceive('pertenceAoUsuario')->once()->with(20, 10)->andReturnTrue();
        $statusRepository = Mockery::mock(StatusQuadroRepository::class);
        $statusRepository->shouldReceive('pertenceAoQuadro')->once()->with(1, 20)->andReturnTrue();

        $resultado = (new CriarTarefaUseCase($repository, $quadroRepository, $statusRepository))
            ->executar(new CriarTarefaDTO('Tarefa', 1, 20, 10));

        $this->assertSame($tarefa, $resultado);
    }

    public function test_atualiza_tarefa(): void
    {
        $tarefa = $this->tarefa();
        $repository = Mockery::mock(TarefaRepository::class);
        $repository->shouldReceive('pertenceAoUsuario')->once()->with(30, 10)->andReturnTrue();
        $repository->shouldReceive('atualizar')->once()->with(30, Mockery::type(Tarefa::class))->andReturn($tarefa);
        $quadroRepository = Mockery::mock(QuadroRepository::class);
        $quadroRepository->shouldReceive('pertenceAoUsuario')->once()->with(20, 10)->andReturnTrue();
        $statusRepository = Mockery::mock(StatusQuadroRepository::class);
        $statusRepository->shouldReceive('pertenceAoQuadro')->once()->with(1, 20)->andReturnTrue();

        $resultado = (new AtualizarTarefaUseCase($repository, $quadroRepository, $statusRepository))
            ->executar(new AtualizarTarefaDTO(30, 'Tarefa', 1, 20, 10));

        $this->assertSame($tarefa, $resultado);
    }

    public function test_apaga_tarefa(): void
    {
        $repository = Mockery::mock(TarefaRepository::class);
        $repository->shouldReceive('pertenceAoUsuario')->once()->with(30, 10)->andReturnTrue();
        $repository->shouldReceive('apagar')->once()->with(30);

        (new ApagarTarefaUseCase($repository))->executar(30, 10);

        $this->addToAssertionCount(1);
    }

    public function test_cria_quadro(): void
    {
        $quadro = $this->quadro();
        $repository = Mockery::mock(QuadroRepository::class);
        $repository->shouldReceive('criar')->once()->with(Mockery::type(Quadro::class))->andReturn($quadro);

        $resultado = (new CriarQuadroUseCase($repository))->executar(new CriarQuadroDTO('Quadro', 10));

        $this->assertSame($quadro, $resultado);
    }

    public function test_atualiza_quadro(): void
    {
        $quadro = $this->quadro();
        $repository = Mockery::mock(QuadroRepository::class);
        $repository->shouldReceive('pertenceAoUsuario')->once()->with(40, 10)->andReturnTrue();
        $repository->shouldReceive('atualizar')->once()->with(40, Mockery::type(Quadro::class))->andReturn($quadro);

        $resultado = (new AtualizarQuadroUseCase($repository))->executar(new AtualizarQuadroDTO(40, 'Quadro', 10));

        $this->assertSame($quadro, $resultado);
    }

    public function test_apaga_quadro(): void
    {
        $repository = Mockery::mock(QuadroRepository::class);
        $repository->shouldReceive('pertenceAoUsuario')->once()->with(40, 10)->andReturnTrue();
        $repository->shouldReceive('apagar')->once()->with(40);

        (new ApagarQuadroUseCase($repository))->executar(40, 10);

        $this->addToAssertionCount(1);
    }

    public function test_lista_status_por_quadro(): void
    {
        $status = [new StatusQuadro(1, 'Pendente', 20)];
        $repository = Mockery::mock(StatusQuadroRepository::class);
        $repository->shouldReceive('listarPorQuadroId')->once()->with(20)->andReturn($status);
        $quadroRepository = Mockery::mock(QuadroRepository::class);
        $quadroRepository->shouldReceive('pertenceAoUsuario')->once()->with(20, 10)->andReturnTrue();

        $resultado = (new ListarStatusQuadroUseCase($repository, $quadroRepository))->executar(20, 10);

        $this->assertSame($status, $resultado);
    }

    public function test_cria_status_do_quadro(): void
    {
        $status = new StatusQuadro(1, 'Pendente', 20);
        $repository = Mockery::mock(StatusQuadroRepository::class);
        $repository->shouldReceive('criar')->once()->with(Mockery::type(StatusQuadro::class))->andReturn($status);
        $quadroRepository = Mockery::mock(QuadroRepository::class);
        $quadroRepository->shouldReceive('pertenceAoUsuario')->once()->with(20, 10)->andReturnTrue();

        $resultado = (new CriarStatusQuadroUseCase($repository, $quadroRepository))
            ->executar(new CriarStatusQuadroDTO(20, 10, 'Pendente'));

        $this->assertSame($status, $resultado);
    }

    public function test_atualiza_status_do_quadro(): void
    {
        $status = new StatusQuadro(1, 'Concluída', 20);
        $repository = Mockery::mock(StatusQuadroRepository::class);
        $repository->shouldReceive('pertenceAoUsuario')->once()->with(1, 10)->andReturnTrue();
        $repository->shouldReceive('atualizar')->once()->with(1, Mockery::type(StatusQuadro::class))->andReturn($status);
        $quadroRepository = Mockery::mock(QuadroRepository::class);
        $quadroRepository->shouldReceive('pertenceAoUsuario')->once()->with(20, 10)->andReturnTrue();

        $resultado = (new AtualizarStatusQuadroUseCase($repository, $quadroRepository))
            ->executar(new AtualizarStatusQuadroDTO(1, 20, 10, 'Concluída'));

        $this->assertSame($status, $resultado);
    }

    public function test_apaga_status_do_quadro(): void
    {
        $repository = Mockery::mock(StatusQuadroRepository::class);
        $repository->shouldReceive('pertenceAoUsuario')->once()->with(1, 10)->andReturnTrue();
        $repository->shouldReceive('apagar')->once()->with(1);

        (new ApagarStatusQuadroUseCase($repository))->executar(1, 10);

        $this->addToAssertionCount(1);
    }

    public function test_lista_tarefas_paginadas_por_status(): void
    {
        $pagina = ['data' => [$this->tarefa()], 'current_page' => 2, 'last_page' => 3, 'total' => 25];
        $repoTarefa = Mockery::mock(TarefaRepository::class);
        $repoTarefa->shouldReceive('listarPorStatus')->once()->with(7, 2)->andReturn($pagina);
        $repoStatus = Mockery::mock(StatusQuadroRepository::class);
        $repoStatus->shouldReceive('pertenceAoUsuario')->once()->with(7, 10)->andReturnTrue();

        $resultado = (new ListarTarefasPorStatusUseCase($repoTarefa, $repoStatus))
            ->executar(new ListarTarefasPorStatusDTO(10, 7, 2));

        $this->assertSame($pagina, $resultado);
    }

    private function tarefa(): Tarefa
    {
        return new Tarefa(null, 'Tarefa', 1, 20);
    }

    private function quadro(): Quadro
    {
        return new Quadro(null, 'Quadro', 10);
    }
}
