<?php

namespace Tests\Unit\Mapeadores;

use App\Mapeadores\UsuarioMapeador;
use App\Models\Usuario as UsuarioModelo;
use Core\Domain\Usuario\Entities\Usuario as UsuarioEntidade;
use DateTimeImmutable;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class UsuarioMapeadorTest extends TestCase
{
    private UsuarioMapeador $mapeador;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mapeador = new UsuarioMapeador;
    }

    // ─── paraEntidade ─────────────────────────────────────────────────────────

    public function test_para_entidade_mapeia_todos_os_campos_escalares(): void
    {
        // O cast 'hashed' do modelo aplica Bcrypt mesmo em instâncias não persistidas;
        // lemos o valor mapeado diretamente do modelo para a comparação.
        $modelo                      = new UsuarioModelo;
        $modelo->id                  = 1;
        $modelo->nome                = 'João Silva';
        $modelo->email               = 'joao@example.com';
        $modelo->senha               = 'hashed_password';
        $modelo->remember_token      = 'tok123';
        $modelo->email_verificado_em = null;
        $modelo->criado_em           = null;
        $modelo->atualizado_em       = null;

        $entidade = $this->mapeador->paraEntidade($modelo);

        $this->assertInstanceOf(UsuarioEntidade::class, $entidade);
        $this->assertSame(1, $entidade->id());
        $this->assertSame('João Silva', $entidade->nome());
        $this->assertSame('joao@example.com', $entidade->email());
        // O mapeador deve repassar exatamente o que o modelo expõe (hash Bcrypt)
        $this->assertSame($modelo->senha, $entidade->senha());
        $this->assertNotEmpty($entidade->senha());
        $this->assertSame('tok123', $entidade->tokenLembranca());
        $this->assertNull($entidade->emailVerificadoEm());
        $this->assertNull($entidade->criadoEm());
        $this->assertNull($entidade->atualizadoEm());
    }

    public function test_para_entidade_converte_datas_carbon_para_datetime_immutable(): void
    {
        $modelo                      = new UsuarioModelo;
        $modelo->id                  = 2;
        $modelo->nome                = 'Maria';
        $modelo->email               = 'maria@example.com';
        $modelo->senha               = 'hashed';
        $modelo->remember_token      = null;
        $modelo->email_verificado_em = Carbon::parse('2026-03-15 10:00:00');
        $modelo->criado_em           = Carbon::parse('2026-01-01 00:00:00');
        $modelo->atualizado_em       = Carbon::parse('2026-06-01 12:00:00');

        $entidade = $this->mapeador->paraEntidade($modelo);

        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->emailVerificadoEm());
        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->criadoEm());
        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->atualizadoEm());
        $this->assertEquals(new DateTimeImmutable('2026-01-01 00:00:00'), $entidade->criadoEm());
        $this->assertEquals(new DateTimeImmutable('2026-06-01 12:00:00'), $entidade->atualizadoEm());
    }

    // ─── paraModelo ───────────────────────────────────────────────────────────

    public function test_para_modelo_cria_novo_modelo_quando_nenhum_fornecido(): void
    {
        $entidade = new UsuarioEntidade(null, 'Lucas', 'lucas@example.com', 'senha');

        $modelo = $this->mapeador->paraModelo($entidade);

        $this->assertInstanceOf(UsuarioModelo::class, $modelo);
        $this->assertSame('Lucas', $modelo->nome);
        $this->assertSame('lucas@example.com', $modelo->email);
    }

    public function test_para_modelo_preenche_modelo_existente_sem_criar_novo(): void
    {
        $modeloExistente     = new UsuarioModelo;
        $modeloExistente->id = 10;

        $entidade = new UsuarioEntidade(10, 'Renomeado', 'novo@email.com', 'senha');

        $retornado = $this->mapeador->paraModelo($entidade, $modeloExistente);

        $this->assertSame($modeloExistente, $retornado, 'Deve retornar a mesma instância do modelo');
        $this->assertSame('Renomeado', $retornado->nome);
        $this->assertSame('novo@email.com', $retornado->email);
    }

    public function test_para_modelo_define_id_no_modelo_quando_entidade_tem_id(): void
    {
        $entidade = new UsuarioEntidade(42, 'Carlos', 'carlos@example.com', 'pass');

        $modelo = $this->mapeador->paraModelo($entidade);

        $this->assertSame(42, $modelo->getKey());
    }

    public function test_para_modelo_nao_define_id_quando_entidade_nao_tem_id(): void
    {
        $entidade = new UsuarioEntidade(null, 'Novo', 'novo@example.com', 'pass');

        $modelo = $this->mapeador->paraModelo($entidade);

        $this->assertNull($modelo->getKey());
    }
}
