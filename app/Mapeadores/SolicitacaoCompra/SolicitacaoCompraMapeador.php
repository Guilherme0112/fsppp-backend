<?php

namespace App\Mapeadores\SolicitacaoCompra;

use App\Models\SolicitacaoCompra\SolicitacaoCompra as SolicitacaoCompraModelo;
use Core\Domain\SolicitacaoCompra\Entities\SolicitacaoCompra as SolicitacaoCompraEntidade;
use Core\Domain\SolicitacaoCompra\Entities\UsuarioResumo;
use DateTimeImmutable;
use DateTimeInterface;

class SolicitacaoCompraMapeador
{
    public function paraEntidade(SolicitacaoCompraModelo $modelo): SolicitacaoCompraEntidade
    {
        return new SolicitacaoCompraEntidade(
            id: $modelo->getKey(),
            titulo: $modelo->titulo,
            valor: $modelo->valor,
            usuario_id: $modelo->usuario_id,
            descricao: $modelo->descricao,
            status: $modelo->status,
            prioridade: $modelo->prioridade ?? 'media',
            prazo: $modelo->prazo,
            motivo_rejeicao: $modelo->motivo_rejeicao,
            recursos_existem: $modelo->recursos_existem,
            status_compra: $modelo->status_compra,
            aprovado_cancelado_por: $modelo->aprovado_cancelado_por,
            concluido_por: $modelo->concluido_por,
            diretoria_por: $modelo->diretoria_por,
            projetos_por: $modelo->projetos_por,
            compras_por: $modelo->compras_por,
            criado_em: $this->paraDataImutavel($modelo->created_at),
            atualizado_em: $this->paraDataImutavel($modelo->updated_at),
            usuario: $this->paraResumo($modelo, 'usuario'),
            aprovado_cancelado_por_usuario: $this->paraResumo($modelo, 'aprovadoCanceladoPorUsuario'),
            concluido_por_usuario: $this->paraResumo($modelo, 'concluidoPorUsuario'),
            diretoria_por_usuario: $this->paraResumo($modelo, 'diretoriaPorUsuario'),
            projetos_por_usuario: $this->paraResumo($modelo, 'projetosPorUsuario'),
            compras_por_usuario: $this->paraResumo($modelo, 'comprasPorUsuario'),
        );
    }

    public function paraModelo(SolicitacaoCompraEntidade $entidade, ?SolicitacaoCompraModelo $modelo = null): SolicitacaoCompraModelo
    {
        $modelo ??= new SolicitacaoCompraModelo;
        $modelo->fill([
            'titulo' => $entidade->titulo(),
            'valor' => $entidade->valor(),
            'prazo' => $entidade->prazo(),
            'descricao' => $entidade->descricao(),
            'usuario_id' => $entidade->usuarioId(),
            'status' => $entidade->status(),
            'prioridade' => $entidade->prioridade(),
            'motivo_rejeicao' => $entidade->motivoRejeicao(),
            'recursos_existem' => $entidade->recursosExistem(),
            'status_compra' => $entidade->statusCompra(),
            'aprovado_cancelado_por' => $entidade->aprovadoCanceladoPor(),
            'concluido_por' => $entidade->concluidoPor(),
            'diretoria_por' => $entidade->diretoriaPor(),
            'projetos_por' => $entidade->projetosPor(),
            'compras_por' => $entidade->comprasPor(),
        ]);

        if ($entidade->id() !== null) {
            $modelo->setAttribute($modelo->getKeyName(), $entidade->id());
        }

        return $modelo;
    }

    private function paraResumo(SolicitacaoCompraModelo $modelo, string $relacao): ?UsuarioResumo
    {
        if (! $modelo->relationLoaded($relacao) || $modelo->{$relacao} === null) {
            return null;
        }

        $usuario = $modelo->{$relacao};

        return new UsuarioResumo(id: $usuario->id, nome: $usuario->nome, email: $usuario->email);
    }

    private function paraDataImutavel(mixed $data): ?DateTimeImmutable
    {
        if ($data === null) {
            return null;
        }

        return $data instanceof DateTimeInterface
            ? DateTimeImmutable::createFromInterface($data)
            : new DateTimeImmutable((string) $data);
    }
}
