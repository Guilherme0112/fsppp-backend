<?php

namespace Core\Application\SolicitacaoCompra\DTOs;

use Core\Domain\SolicitacaoCompra\Entities\SolicitacaoCompra;

readonly class AtualizarSolicitacaoCompraDTO
{
    public function __construct(
        public int $id,
        public string $titulo,
        public int $valor,
        public int $usuarioId,
        public ?string $descricao = null,
        public string $prioridade = 'media',
        public ?string $prazo = null,
    ) {}

    public static function deArray(array $dados, int $id, int $usuarioId): self
    {
        return new self(
            id: $id,
            titulo: $dados['titulo'],
            valor: $dados['valor'],
            usuarioId: $usuarioId,
            descricao: $dados['descricao'] ?? null,
            prioridade: $dados['prioridade'] ?? 'media',
            prazo: $dados['prazo'] ?? null,
        );
    }

    public function paraEntidade(): SolicitacaoCompra
    {
        return new SolicitacaoCompra(
            id: $this->id,
            titulo: $this->titulo,
            valor: $this->valor,
            usuario_id: $this->usuarioId,
            descricao: $this->descricao,
            prioridade: $this->prioridade,
            prazo: $this->prazo,
        );
    }
}
