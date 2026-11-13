<?php

namespace Tests\Unit\Application\SolicitacaoCompra;

use Core\Application\SolicitacaoCompra\DTOs\AtualizarStatusCompraDTO;
use Core\Application\SolicitacaoCompra\DTOs\AvaliarDiretoriaDTO;
use Core\Application\SolicitacaoCompra\DTOs\AvaliarProjetosDTO;
use Core\Application\SolicitacaoCompra\DTOs\EnviarParaDiretoriaDTO;
use Core\Application\SolicitacaoCompra\UseCases\AtualizarStatusCompraUseCase;
use Core\Application\SolicitacaoCompra\UseCases\AvaliarDiretoriaUseCase;
use Core\Application\SolicitacaoCompra\UseCases\AvaliarProjetosUseCase;
use Core\Application\SolicitacaoCompra\UseCases\EnviarParaDiretoriaUseCase;
use Core\Domain\SolicitacaoCompra\Entities\SolicitacaoCompra;
use Core\Domain\SolicitacaoCompra\Exceptions\SolicitacaoNaoEncontradaException;
use Core\Domain\SolicitacaoCompra\Exceptions\TransicaoStatusInvalidaException;
use Core\Domain\SolicitacaoCompra\Repositories\SolicitacaoCompraRepository;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class SolicitacaoCompraUseCasesTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private function solicitacao(string $status = SolicitacaoCompra::STATUS_PENDENTE, int $usuarioId = 1): SolicitacaoCompra
    {
        return new SolicitacaoCompra(
            id: 10,
            titulo: 'Teste Compra',
            valor: 15000,
            usuario_id: $usuarioId,
            status: $status,
        );
    }

    public function test_enviar_para_diretoria_com_sucesso(): void
    {
        $solicitacao = $this->solicitacao(SolicitacaoCompra::STATUS_PENDENTE, 1);
        $repo = Mockery::mock(SolicitacaoCompraRepository::class);
        $repo->shouldReceive('buscarPorId')->once()->with(10)->andReturn($solicitacao);
        $repo->shouldReceive('enviarParaDiretoria')->once()->with(10, 1)->andReturn($solicitacao);

        $useCase = new EnviarParaDiretoriaUseCase($repo);
        $resultado = $useCase->executar(new EnviarParaDiretoriaDTO(10, 1));

        $this->assertInstanceOf(SolicitacaoCompra::class, $resultado);
    }

    public function test_enviar_para_diretoria_lanca_excecao_quando_nao_encontrada(): void
    {
        $this->expectException(SolicitacaoNaoEncontradaException::class);

        $repo = Mockery::mock(SolicitacaoCompraRepository::class);
        $repo->shouldReceive('buscarPorId')->once()->with(99)->andReturn(null);

        (new EnviarParaDiretoriaUseCase($repo))->executar(new EnviarParaDiretoriaDTO(99, 1));
    }

    public function test_avaliar_diretoria_rejeicao_exige_motivo(): void
    {
        $this->expectException(TransicaoStatusInvalidaException::class);

        $solicitacao = $this->solicitacao(SolicitacaoCompra::STATUS_ENVIADO_DIRETORIA, 1);
        $repo = Mockery::mock(SolicitacaoCompraRepository::class);
        $repo->shouldReceive('buscarPorId')->once()->with(10)->andReturn($solicitacao);

        (new AvaliarDiretoriaUseCase($repo))->executar(new AvaliarDiretoriaDTO(
            id: 10,
            aprovado: false,
            prioridade: 'alta',
            motivoRejeicao: '',
            usuarioId: 2
        ));
    }

    public function test_avaliar_diretoria_aceita_solicitacao_pendente(): void
    {
        $solicitacao = $this->solicitacao(SolicitacaoCompra::STATUS_PENDENTE, 1);
        $repo = Mockery::mock(SolicitacaoCompraRepository::class);
        $repo->shouldReceive('buscarPorId')->once()->with(10)->andReturn($solicitacao);
        $repo->shouldReceive('avaliarDiretoria')->once()->with(10, true, 'alta', null, 2)->andReturn($solicitacao);

        $resultado = (new AvaliarDiretoriaUseCase($repo))->executar(new AvaliarDiretoriaDTO(
            id: 10,
            aprovado: true,
            prioridade: 'alta',
            motivoRejeicao: null,
            usuarioId: 2
        ));

        $this->assertInstanceOf(SolicitacaoCompra::class, $resultado);
    }

    public function test_avaliar_projetos_com_sucesso(): void
    {
        $solicitacao = $this->solicitacao(SolicitacaoCompra::STATUS_ANALISE_PROJETOS, 1);
        $repo = Mockery::mock(SolicitacaoCompraRepository::class);
        $repo->shouldReceive('buscarPorId')->once()->with(10)->andReturn($solicitacao);
        $repo->shouldReceive('avaliarProjetos')->once()->with(10, true, 5)->andReturn($solicitacao);

        $resultado = (new AvaliarProjetosUseCase($repo))->executar(new AvaliarProjetosDTO(10, true, 5));

        $this->assertInstanceOf(SolicitacaoCompra::class, $resultado);
    }

    public function test_atualizar_status_compra_com_sucesso(): void
    {
        $solicitacao = $this->solicitacao(SolicitacaoCompra::STATUS_RECURSOS_DISPONIVEIS_COMPRAS, 1);
        $repo = Mockery::mock(SolicitacaoCompraRepository::class);
        $repo->shouldReceive('buscarPorId')->once()->with(10)->andReturn($solicitacao);
        $repo->shouldReceive('atualizarStatusCompra')->once()->with(10, 'Comprado', 3)->andReturn($solicitacao);

        $resultado = (new AtualizarStatusCompraUseCase($repo))->executar(new AtualizarStatusCompraDTO(10, 'Comprado', 3));

        $this->assertInstanceOf(SolicitacaoCompra::class, $resultado);
    }
}
