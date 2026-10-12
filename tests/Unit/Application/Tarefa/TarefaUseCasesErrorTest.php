<?php

namespace Tests\Unit\Application\Tarefa;

use Core\Application\Tarefa\DTOs\AtualizarQuadroDTO;
use Core\Application\Tarefa\DTOs\AtualizarStatusQuadroDTO;
use Core\Application\Tarefa\DTOs\AtualizarTarefaDTO;
use Core\Application\Tarefa\DTOs\CriarStatusQuadroDTO;
use Core\Application\Tarefa\DTOs\CriarTarefaDTO;
use Core\Application\Tarefa\UseCases\ApagarQuadroUseCase;
use Core\Application\Tarefa\UseCases\ApagarStatusQuadroUseCase;
use Core\Application\Tarefa\UseCases\ApagarTarefaUseCase;
use Core\Application\Tarefa\UseCases\AtualizarQuadroUseCase;
use Core\Application\Tarefa\UseCases\AtualizarStatusQuadroUseCase;
use Core\Application\Tarefa\UseCases\AtualizarTarefaUseCase;
use Core\Application\Tarefa\UseCases\CriarStatusQuadroUseCase;
use Core\Application\Tarefa\UseCases\CriarTarefaUseCase;
use Core\Application\Tarefa\UseCases\ListarStatusQuadroUseCase;
use Core\Domain\Tarefa\Exceptions\RecursoNaoEncontradoException;
use Core\Domain\Tarefa\Repositories\QuadroRepository;
use Core\Domain\Tarefa\Repositories\StatusQuadroRepository;
use Core\Domain\Tarefa\Repositories\TarefaRepository;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class TarefaUseCasesErrorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_atualizar_quadro_lanca_excecao_quando_nao_pertence_ao_usuario(): void
    {
        $repo = Mockery::mock(QuadroRepository::class);
        $repo->shouldReceive('pertenceAoUsuario')->once()->with(1, 10)->andReturnFalse();

        $this->expectException(RecursoNaoEncontradoException::class);
        (new AtualizarQuadroUseCase($repo))->executar(new AtualizarQuadroDTO(1, 'Nome', 10));
    }

    public function test_apagar_quadro_lanca_excecao_quando_nao_pertence_ao_usuario(): void
    {
        $repo = Mockery::mock(QuadroRepository::class);
        $repo->shouldReceive('pertenceAoUsuario')->once()->with(1, 10)->andReturnFalse();

        $this->expectException(RecursoNaoEncontradoException::class);
        (new ApagarQuadroUseCase($repo))->executar(1, 10);
    }

    public function test_listar_status_lanca_excecao_quando_quadro_nao_pertence_ao_usuario(): void
    {
        $repoStatus = Mockery::mock(StatusQuadroRepository::class);
        $repoQuadro = Mockery::mock(QuadroRepository::class);
        $repoQuadro->shouldReceive('pertenceAoUsuario')->once()->with(1, 10)->andReturnFalse();

        $this->expectException(RecursoNaoEncontradoException::class);
        (new ListarStatusQuadroUseCase($repoStatus, $repoQuadro))->executar(1, 10);
    }

    public function test_criar_status_lanca_excecao_quando_quadro_nao_pertence_ao_usuario(): void
    {
        $repoStatus = Mockery::mock(StatusQuadroRepository::class);
        $repoQuadro = Mockery::mock(QuadroRepository::class);
        $repoQuadro->shouldReceive('pertenceAoUsuario')->once()->with(1, 10)->andReturnFalse();

        $this->expectException(RecursoNaoEncontradoException::class);
        (new CriarStatusQuadroUseCase($repoStatus, $repoQuadro))->executar(new CriarStatusQuadroDTO(1, 10, 'A Fazer'));
    }

    public function test_atualizar_status_lanca_excecao_quando_status_nao_pertence_ao_usuario(): void
    {
        $repoStatus = Mockery::mock(StatusQuadroRepository::class);
        $repoQuadro = Mockery::mock(QuadroRepository::class);
        $repoStatus->shouldReceive('pertenceAoUsuario')->once()->with(5, 10)->andReturnFalse();

        $this->expectException(RecursoNaoEncontradoException::class);
        (new AtualizarStatusQuadroUseCase($repoStatus, $repoQuadro))
            ->executar(new AtualizarStatusQuadroDTO(5, 1, 10, 'A Fazer'));
    }

    public function test_atualizar_status_lanca_excecao_quando_quadro_nao_pertence_ao_usuario(): void
    {
        $repoStatus = Mockery::mock(StatusQuadroRepository::class);
        $repoQuadro = Mockery::mock(QuadroRepository::class);
        $repoStatus->shouldReceive('pertenceAoUsuario')->once()->with(5, 10)->andReturnTrue();
        $repoQuadro->shouldReceive('pertenceAoUsuario')->once()->with(1, 10)->andReturnFalse();

        $this->expectException(RecursoNaoEncontradoException::class);
        (new AtualizarStatusQuadroUseCase($repoStatus, $repoQuadro))
            ->executar(new AtualizarStatusQuadroDTO(5, 1, 10, 'A Fazer'));
    }

    public function test_apagar_status_lanca_excecao_quando_status_nao_pertence_ao_usuario(): void
    {
        $repoStatus = Mockery::mock(StatusQuadroRepository::class);
        $repoStatus->shouldReceive('pertenceAoUsuario')->once()->with(5, 10)->andReturnFalse();

        $this->expectException(RecursoNaoEncontradoException::class);
        (new ApagarStatusQuadroUseCase($repoStatus))->executar(5, 10);
    }

    public function test_criar_tarefa_lanca_excecao_quando_quadro_nao_pertence_ao_usuario(): void
    {
        $repoTarefa = Mockery::mock(TarefaRepository::class);
        $repoQuadro = Mockery::mock(QuadroRepository::class);
        $repoStatus = Mockery::mock(StatusQuadroRepository::class);
        $repoQuadro->shouldReceive('pertenceAoUsuario')->once()->with(1, 10)->andReturnFalse();

        $this->expectException(RecursoNaoEncontradoException::class);
        (new CriarTarefaUseCase($repoTarefa, $repoQuadro, $repoStatus))
            ->executar(new CriarTarefaDTO('Tarefa', 2, 1, 10));
    }

    public function test_criar_tarefa_lanca_excecao_quando_status_nao_pertence_ao_quadro(): void
    {
        $repoTarefa = Mockery::mock(TarefaRepository::class);
        $repoQuadro = Mockery::mock(QuadroRepository::class);
        $repoStatus = Mockery::mock(StatusQuadroRepository::class);
        $repoQuadro->shouldReceive('pertenceAoUsuario')->once()->with(1, 10)->andReturnTrue();
        $repoStatus->shouldReceive('pertenceAoQuadro')->once()->with(2, 1)->andReturnFalse();

        $this->expectException(RecursoNaoEncontradoException::class);
        (new CriarTarefaUseCase($repoTarefa, $repoQuadro, $repoStatus))
            ->executar(new CriarTarefaDTO('Tarefa', 2, 1, 10));
    }

    public function test_atualizar_tarefa_lanca_excecao_quando_tarefa_nao_pertence_ao_usuario(): void
    {
        $repoTarefa = Mockery::mock(TarefaRepository::class);
        $repoQuadro = Mockery::mock(QuadroRepository::class);
        $repoStatus = Mockery::mock(StatusQuadroRepository::class);
        $repoTarefa->shouldReceive('pertenceAoUsuario')->once()->with(10, 10)->andReturnFalse();

        $this->expectException(RecursoNaoEncontradoException::class);
        (new AtualizarTarefaUseCase($repoTarefa, $repoQuadro, $repoStatus))
            ->executar(new AtualizarTarefaDTO(10, 'Tarefa', 2, 1, 10));
    }

    public function test_atualizar_tarefa_lanca_excecao_quando_quadro_nao_pertence_ao_usuario(): void
    {
        $repoTarefa = Mockery::mock(TarefaRepository::class);
        $repoQuadro = Mockery::mock(QuadroRepository::class);
        $repoStatus = Mockery::mock(StatusQuadroRepository::class);
        $repoTarefa->shouldReceive('pertenceAoUsuario')->once()->with(10, 10)->andReturnTrue();
        $repoQuadro->shouldReceive('pertenceAoUsuario')->once()->with(1, 10)->andReturnFalse();

        $this->expectException(RecursoNaoEncontradoException::class);
        (new AtualizarTarefaUseCase($repoTarefa, $repoQuadro, $repoStatus))
            ->executar(new AtualizarTarefaDTO(10, 'Tarefa', 2, 1, 10));
    }

    public function test_atualizar_tarefa_lanca_excecao_quando_status_nao_pertence_ao_quadro(): void
    {
        $repoTarefa = Mockery::mock(TarefaRepository::class);
        $repoQuadro = Mockery::mock(QuadroRepository::class);
        $repoStatus = Mockery::mock(StatusQuadroRepository::class);
        $repoTarefa->shouldReceive('pertenceAoUsuario')->once()->with(10, 10)->andReturnTrue();
        $repoQuadro->shouldReceive('pertenceAoUsuario')->once()->with(1, 10)->andReturnTrue();
        $repoStatus->shouldReceive('pertenceAoQuadro')->once()->with(2, 1)->andReturnFalse();

        $this->expectException(RecursoNaoEncontradoException::class);
        (new AtualizarTarefaUseCase($repoTarefa, $repoQuadro, $repoStatus))
            ->executar(new AtualizarTarefaDTO(10, 'Tarefa', 2, 1, 10));
    }

    public function test_apagar_tarefa_lanca_excecao_quando_tarefa_nao_pertence_ao_usuario(): void
    {
        $repoTarefa = Mockery::mock(TarefaRepository::class);
        $repoTarefa->shouldReceive('pertenceAoUsuario')->once()->with(10, 10)->andReturnFalse();

        $this->expectException(RecursoNaoEncontradoException::class);
        (new ApagarTarefaUseCase($repoTarefa))->executar(10, 10);
    }
}
