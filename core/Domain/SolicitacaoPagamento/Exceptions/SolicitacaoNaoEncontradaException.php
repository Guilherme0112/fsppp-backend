<?php

namespace Core\Domain\SolicitacaoPagamento\Exceptions;

use RuntimeException;

class SolicitacaoNaoEncontradaException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Solicitação de pagamento não encontrada.');
    }
}
