<?php

namespace Tests\Unit\Application\Usuario;

use Core\Application\Usuario\DTOs\AtualizarUsuarioDTO;
use Core\Application\Usuario\DTOs\CriarUsuarioDTO;
use Core\Application\Usuario\DTOs\ListarUsuariosDTO;
use Core\Domain\Usuario\Entities\Usuario;
use PHPUnit\Framework\TestCase;

class UsuarioDTOsTest extends TestCase
{
    // ─── CriarUsuarioDTO ─────────────────────────────────────────────────────

    public function test_criar_usuario_dto_da_requisicao_mapeia_campos(): void
    {
        $dados = [
            'nome'  => 'João Silva',
            'email' => 'joao@example.com',
            'senha' => 'segredo123',
        ];

        $dto = CriarUsuarioDTO::daRequisicao($dados);

        $this->assertSame('João Silva', $dto->nome);
        $this->assertSame('joao@example.com', $dto->email);
        $this->assertSame('segredo123', $dto->senha);
    }

    public function test_criar_usuario_dto_para_entidade_retorna_usuario_sem_id(): void
    {
        $dto = new CriarUsuarioDTO('Maria', 'maria@example.com', 'abc123');

        $entidade = $dto->paraEntidade();

        $this->assertInstanceOf(Usuario::class, $entidade);
        $this->assertNull($entidade->id(), 'Novo usuário deve ter id null');
        $this->assertSame('Maria', $entidade->nome());
        $this->assertSame('maria@example.com', $entidade->email());
        $this->assertSame('abc123', $entidade->senha());
    }

    // ─── AtualizarUsuarioDTO ──────────────────────────────────────────────────

    public function test_atualizar_usuario_dto_da_requisicao_com_senha(): void
    {
        $dados = [
            'nome'  => 'João Atualizado',
            'email' => 'joao@novo.com',
            'senha' => 'novaSenha',
        ];

        $dto = AtualizarUsuarioDTO::daRequisicao($dados, 5);

        $this->assertSame(5, $dto->id);
        $this->assertSame('João Atualizado', $dto->nome);
        $this->assertSame('joao@novo.com', $dto->email);
        $this->assertSame('novaSenha', $dto->senha);
    }

    public function test_atualizar_usuario_dto_da_requisicao_sem_senha_retorna_null(): void
    {
        $dados = ['nome' => 'Sem Senha', 'email' => 'semsenha@example.com'];

        $dto = AtualizarUsuarioDTO::daRequisicao($dados, 3);

        $this->assertSame(3, $dto->id);
        $this->assertNull($dto->senha, 'Senha deve ser null quando não enviada');
    }

    public function test_atualizar_usuario_dto_para_entidade_retorna_usuario_com_id(): void
    {
        $dto = new AtualizarUsuarioDTO(7, 'Lucas', 'lucas@example.com', 'pass');

        $entidade = $dto->paraEntidade();

        $this->assertInstanceOf(Usuario::class, $entidade);
        $this->assertSame(7, $entidade->id());
        $this->assertSame('Lucas', $entidade->nome());
        $this->assertSame('lucas@example.com', $entidade->email());
        $this->assertSame('pass', $entidade->senha());
    }

    public function test_atualizar_usuario_dto_para_entidade_com_senha_null_usa_string_vazia(): void
    {
        // Garante que paraEntidade() não quebra com senha null
        $dto = new AtualizarUsuarioDTO(1, 'Nome', 'email@test.com', null);

        $entidade = $dto->paraEntidade();

        $this->assertSame('', $entidade->senha());
    }

    // ─── ListarUsuariosDTO ────────────────────────────────────────────────────

    public function test_listar_usuarios_dto_da_requisicao_captura_usuario_id(): void
    {
        $dto = ListarUsuariosDTO::daRequisicao([], 42);

        $this->assertSame(42, $dto->usuarioId);
    }
}
