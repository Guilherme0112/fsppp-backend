<?php

namespace Tests\Unit\Application\Usuario;

use Core\Application\Usuario\DTOs\AtualizarUsuarioDTO;
use Core\Application\Usuario\DTOs\CriarUsuarioDTO;
use Core\Application\Usuario\DTOs\ListarUsuariosDTO;
use Core\Application\Usuario\UseCases\ApagarUsuarioUseCase;
use Core\Application\Usuario\UseCases\AtualizarUsuarioUseCase;
use Core\Application\Usuario\UseCases\CriarUsuarioUseCase;
use Core\Application\Usuario\UseCases\ListarUsuariosUseCase;
use Core\Domain\Usuario\Entities\Usuario;
use Core\Domain\Usuario\Repositories\UsuarioRepository;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class UsuarioUseCasesTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    // ─── ListarUsuariosUseCase ────────────────────────────────────────────────

    public function test_listar_delega_ao_repositorio_e_retorna_array(): void
    {
        $lista = [$this->usuario()];
        $repo  = Mockery::mock(UsuarioRepository::class);
        $repo->shouldReceive('listar')->once()->andReturn($lista);

        $resultado = (new ListarUsuariosUseCase($repo))->executar(new ListarUsuariosDTO(1));

        $this->assertSame($lista, $resultado);
    }

    public function test_listar_retorna_array_vazio_quando_nao_ha_usuarios(): void
    {
        $repo = Mockery::mock(UsuarioRepository::class);
        $repo->shouldReceive('listar')->once()->andReturn([]);

        $resultado = (new ListarUsuariosUseCase($repo))->executar(new ListarUsuariosDTO(1));

        $this->assertSame([], $resultado);
    }

    // ─── CriarUsuarioUseCase ──────────────────────────────────────────────────

    public function test_criar_delega_ao_repositorio_com_entidade_correta(): void
    {
        $usuario = $this->usuario();
        $repo    = Mockery::mock(UsuarioRepository::class);
        $repo->shouldReceive('criar')
            ->once()
            ->with(Mockery::type(Usuario::class))
            ->andReturn($usuario);

        $dto       = new CriarUsuarioDTO('João', 'joao@example.com', 'senha123');
        $resultado = (new CriarUsuarioUseCase($repo))->executar($dto);

        $this->assertSame($usuario, $resultado);
    }

    public function test_criar_passa_entidade_sem_id_ao_repositorio(): void
    {
        $repo = Mockery::mock(UsuarioRepository::class);
        $repo->shouldReceive('criar')
            ->once()
            ->with(Mockery::on(fn (Usuario $u) => $u->id() === null && $u->nome() === 'Maria'))
            ->andReturn($this->usuario());

        (new CriarUsuarioUseCase($repo))->executar(new CriarUsuarioDTO('Maria', 'm@example.com', 'pass'));

        $this->addToAssertionCount(1);
    }

    // ─── AtualizarUsuarioUseCase ──────────────────────────────────────────────

    public function test_atualizar_delega_ao_repositorio_com_id_e_entidade(): void
    {
        $usuario = $this->usuario();
        $repo    = Mockery::mock(UsuarioRepository::class);
        $repo->shouldReceive('atualizar')
            ->once()
            ->with(7, Mockery::type(Usuario::class))
            ->andReturn($usuario);

        $dto       = new AtualizarUsuarioDTO(7, 'João', 'joao@example.com', 'novasenha');
        $resultado = (new AtualizarUsuarioUseCase($repo))->executar($dto);

        $this->assertSame($usuario, $resultado);
    }

    public function test_atualizar_usa_id_do_dto_como_primeiro_argumento(): void
    {
        $repo = Mockery::mock(UsuarioRepository::class);
        $repo->shouldReceive('atualizar')
            ->once()
            ->with(99, Mockery::type(Usuario::class))
            ->andReturn($this->usuario());

        (new AtualizarUsuarioUseCase($repo))->executar(
            new AtualizarUsuarioDTO(99, 'Nome', 'email@test.com', 'pass')
        );

        $this->addToAssertionCount(1);
    }

    // ─── ApagarUsuarioUseCase ─────────────────────────────────────────────────

    public function test_apagar_delega_ao_repositorio_com_id_correto(): void
    {
        $repo = Mockery::mock(UsuarioRepository::class);
        $repo->shouldReceive('apagar')->once()->with(5);

        (new ApagarUsuarioUseCase($repo))->executar(5);

        $this->addToAssertionCount(1);
    }

    public function test_apagar_nao_retorna_valor(): void
    {
        $repo = Mockery::mock(UsuarioRepository::class);
        $repo->shouldReceive('apagar')->once()->with(1);

        $resultado = (new ApagarUsuarioUseCase($repo))->executar(1);

        $this->assertNull($resultado);
    }

    // ─── Helper ───────────────────────────────────────────────────────────────

    private function usuario(): Usuario
    {
        return new Usuario(1, 'João', 'joao@example.com', 'hashed');
    }
}
