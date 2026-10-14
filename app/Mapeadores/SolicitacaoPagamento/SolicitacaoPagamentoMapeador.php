<?php

namespace App\Mapeadores\SolicitacaoPagamento;

use App\Models\SolicitacaoPagamento\SolicitacaoPagamento as SolicitacaoPagamentoModelo;
use Core\Domain\SolicitacaoPagamento\Entities\SolicitacaoPagamento as SolicitacaoPagamentoEntidade;
use Core\Domain\SolicitacaoPagamento\Entities\UsuarioResumo;
use DateTimeImmutable;
use DateTimeInterface;

class SolicitacaoPagamentoMapeador
{
    public function paraEntidade(SolicitacaoPagamentoModelo $modelo): SolicitacaoPagamentoEntidade
    {
        return new SolicitacaoPagamentoEntidade(
            id: $modelo->getKey(),
            titulo: $modelo->titulo,
            valor: $modelo->valor,
            usuario_id: $modelo->usuario_id,
            descricao: $modelo->descricao,
            status: $modelo->status,
            aprovado_cancelado_por: $modelo->aprovado_cancelado_por,
            concluido_por: $modelo->concluido_por,
            criado_em: $this->paraDataImutavel($modelo->created_at),
            atualizado_em: $this->paraDataImutavel($modelo->updated_at),
            usuario: $this->paraResumo($modelo, 'usuario'),
            aprovado_cancelado_por_usuario: $this->paraResumo($modelo, 'aprovadoCanceladoPorUsuario'),
            concluido_por_usuario: $this->paraResumo($modelo, 'concluidoPorUsuario'),
        );
    }

    public function paraModelo(SolicitacaoPagamentoEntidade $entidade, ?SolicitacaoPagamentoModelo $modelo = null): SolicitacaoPagamentoModelo
    {
        $modelo ??= new SolicitacaoPagamentoModelo;
        $modelo->fill([
            'titulo' => $entidade->titulo(),
            'valor' => $entidade->valor(),
            'descricao' => $entidade->descricao(),
            'usuario_id' => $entidade->usuarioId(),
            'status' => $entidade->status(),
            'aprovado_cancelado_por' => $entidade->aprovadoCanceladoPor(),
            'concluido_por' => $entidade->concluidoPor(),
        ]);

        if ($entidade->id() !== null) {
            $modelo->setAttribute($modelo->getKeyName(), $entidade->id());
        }

        return $modelo;
    }

    // Builds the nested requester/approver/completer summary from an eager-loaded
    // relation only — never touches it when the relation wasn't loaded, so this
    // never triggers a surprise lazy query for callers that don't need it (criar/atualizar).
    private function paraResumo(SolicitacaoPagamentoModelo $modelo, string $relacao): ?UsuarioResumo
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
