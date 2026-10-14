<?php

namespace Core\Application\SolicitacaoPagamento\DTOs;

use Core\Domain\SolicitacaoPagamento\Entities\SolicitacaoPagamento;

readonly class CriarSolicitacaoPagamentoDTO
{
    public function __construct(
        public string $titulo,
        public int $valor,
        public int $usuarioId,
        public ?string $descricao = null,
    ) {}

    public static function daRequisicao(array $dados, int $usuarioId): self
    {
        return new self(
            titulo: $dados['titulo'],
            valor: $dados['valor'],
            usuarioId: $usuarioId,
            descricao: $dados['descricao'] ?? null,
        );
    }

    public function paraEntidade(): SolicitacaoPagamento
    {
        return new SolicitacaoPagamento(
            id: null,
            titulo: $this->titulo,
            valor: $this->valor,
            usuario_id: $this->usuarioId,
            descricao: $this->descricao,
        );
    }
}
