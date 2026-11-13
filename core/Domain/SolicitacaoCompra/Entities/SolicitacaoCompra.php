<?php

namespace Core\Domain\SolicitacaoCompra\Entities;

class SolicitacaoCompra implements \JsonSerializable
{
    public const STATUS_PENDENTE = 'pendente';
    public const STATUS_APROVADO = 'aprovado';
    public const STATUS_ENVIADO_DIRETORIA = 'enviado_diretoria';
    public const STATUS_APROVADO_DIRETORIA = 'aprovado_diretoria';
    public const STATUS_REJEITADO_DIRETORIA = 'rejeitado_diretoria';
    public const STATUS_ANALISE_PROJETOS = 'analise_projetos';
    public const STATUS_RECURSOS_DISPONIVEIS_COMPRAS = 'recursos_disponiveis_compras';
    public const STATUS_RECURSOS_FUNDACAO = 'recursos_fundacao';
    public const STATUS_CONCLUIDO = 'concluido';
    public const STATUS_CANCELADO = 'cancelado';

    public function __construct(
        private readonly ?int $id,
        private readonly string $titulo,
        private readonly int $valor,
        private readonly int $usuario_id,
        private readonly ?string $descricao = null,
        private readonly string $status = self::STATUS_PENDENTE,
        private readonly string $prioridade = 'media',
        private readonly ?string $prazo = null,
        private readonly ?string $motivo_rejeicao = null,
        private readonly ?bool $recursos_existem = null,
        private readonly ?string $status_compra = null,
        private readonly ?int $aprovado_cancelado_por = null,
        private readonly ?int $concluido_por = null,
        private readonly ?int $diretoria_por = null,
        private readonly ?int $projetos_por = null,
        private readonly ?int $compras_por = null,
        private readonly ?\DateTimeImmutable $criado_em = null,
        private readonly ?\DateTimeImmutable $atualizado_em = null,
        private readonly ?UsuarioResumo $usuario = null,
        private readonly ?UsuarioResumo $aprovado_cancelado_por_usuario = null,
        private readonly ?UsuarioResumo $concluido_por_usuario = null,
        private readonly ?UsuarioResumo $diretoria_por_usuario = null,
        private readonly ?UsuarioResumo $projetos_por_usuario = null,
        private readonly ?UsuarioResumo $compras_por_usuario = null,
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

    public function prioridade(): string
    {
        return $this->prioridade;
    }

    public function prazo(): ?string
    {
        return $this->prazo;
    }

    public function motivoRejeicao(): ?string
    {
        return $this->motivo_rejeicao;
    }

    public function recursosExistem(): ?bool
    {
        return $this->recursos_existem;
    }

    public function statusCompra(): ?string
    {
        return $this->status_compra;
    }

    public function aprovadoCanceladoPor(): ?int
    {
        return $this->aprovado_cancelado_por;
    }

    public function concluidoPor(): ?int
    {
        return $this->concluido_por;
    }

    public function diretoriaPor(): ?int
    {
        return $this->diretoria_por;
    }

    public function projetosPor(): ?int
    {
        return $this->projetos_por;
    }

    public function comprasPor(): ?int
    {
        return $this->compras_por;
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
            'prioridade' => $this->prioridade,
            'prazo' => $this->prazo,
            'motivo_rejeicao' => $this->motivo_rejeicao,
            'recursos_existem' => $this->recursos_existem,
            'status_compra' => $this->status_compra,
            'aprovado_cancelado_por' => $this->aprovado_cancelado_por,
            'concluido_por' => $this->concluido_por,
            'diretoria_por' => $this->diretoria_por,
            'projetos_por' => $this->projetos_por,
            'compras_por' => $this->compras_por,
            'created_at' => $this->criado_em?->format(DATE_ATOM),
            'updated_at' => $this->atualizado_em?->format(DATE_ATOM),
            'usuario' => $this->usuario?->paraArray(),
            'aprovado_cancelado_por_usuario' => $this->aprovado_cancelado_por_usuario?->paraArray(),
            'concluido_por_usuario' => $this->concluido_por_usuario?->paraArray(),
            'diretoria_por_usuario' => $this->diretoria_por_usuario?->paraArray(),
            'projetos_por_usuario' => $this->projetos_por_usuario?->paraArray(),
            'compras_por_usuario' => $this->compras_por_usuario?->paraArray(),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->paraArray();
    }
}
