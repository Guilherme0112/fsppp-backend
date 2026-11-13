<?php

namespace Core\Application\Tarefa\DTOs;

use Core\Domain\Tarefa\Entities\Tarefa;
use DateTimeImmutable;

readonly class AtualizarTarefaDTO
{
    public function __construct(
        public int $id,
        public string $nome,
        public int $statusId,
        public int $quadroId,
        public int $usuarioId,
        public ?string $descricao = null,
        public ?DateTimeImmutable $dataInicio = null,
        public ?DateTimeImmutable $dataFim = null,
    ) {}

    public static function deArray(array $dados, int $id, int $usuarioId): self
    {
        return new self(
            id: $id,
            nome: $dados['nome'],
            statusId: $dados['status_id'],
            quadroId: $dados['quadro_id'],
            usuarioId: $usuarioId,
            descricao: $dados['descricao'] ?? null,
            dataInicio: isset($dados['data_inicio']) ? new DateTimeImmutable($dados['data_inicio']) : null,
            dataFim: isset($dados['data_fim']) ? new DateTimeImmutable($dados['data_fim']) : null,
        );
    }

    public function paraEntidade(): Tarefa
    {
        return new Tarefa($this->id, $this->nome, $this->statusId, $this->quadroId, $this->descricao, $this->dataInicio, $this->dataFim);
    }
}
