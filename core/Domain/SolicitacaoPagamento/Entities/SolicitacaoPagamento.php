<?php

namespace Core\Domain\SolicitacaoPagamento\Entities;

class SolicitacaoPagamento implements \JsonSerializable
{
    public const STATUS_PENDENTE = 'pendente';

    public const STATUS_APROVADO = 'aprovado';

    public const STATUS_CONCLUIDO = 'concluido';

    public const STATUS_CANCELADO = 'cancelado';

    public function __construct(
        private readonly ?int $id,
        private readonly string $titulo,
        private readonly int $valor,
        private readonly int $usuario_id,
        private readonly ?string $descricao = null,
        private readonly string $status = self::STATUS_PENDENTE,
        private readonly ?int $aprovado_cancelado_por = null,
        private readonly ?int $concluido_por = null,
        private readonly ?\DateTimeImmutable $criado_em = null,
        private readonly ?\DateTimeImmutable $atualizado_em = null,
        private readonly ?UsuarioResumo $usuario = null,
        private readonly ?UsuarioResumo $aprovado_cancelado_por_usuario = null,
        private readonly ?UsuarioResumo $concluido_por_usuario = null,
    ) {}

    public function id(): ?int
    {
        return $this->id;
    }

    public function titulo(): string
    {
        return $this->titulo;
    }

    public function valor(): int
    {
        return $this->valor;
    }

    public function usuarioId(): int
    {
        return $this->usuario_id;
    }

    public function descricao(): ?string
    {
        return $this->descricao;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function aprovadoCanceladoPor(): ?int
    {
        return $this->aprovado_cancelado_por;
    }

    public function concluidoPor(): ?int
    {
        return $this->concluido_por;
    }

    public function criadoEm(): ?\DateTimeImmutable
    {
        return $this->criado_em;
    }

    public function atualizadoEm(): ?\DateTimeImmutable
    {
        return $this->atualizado_em;
    }

    public function paraArray(): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'valor' => $this->valor,
            'descricao' => $this->descricao,
            'usuario_id' => $this->usuario_id,
            'status' => $this->status,
            'aprovado_cancelado_por' => $this->aprovado_cancelado_por,
            'concluido_por' => $this->concluido_por,
            'created_at' => $this->criado_em?->format(DATE_ATOM),
            'updated_at' => $this->atualizado_em?->format(DATE_ATOM),
            'usuario' => $this->usuario?->paraArray(),
            'aprovado_cancelado_por_usuario' => $this->aprovado_cancelado_por_usuario?->paraArray(),
            'concluido_por_usuario' => $this->concluido_por_usuario?->paraArray(),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->paraArray();
    }
}
