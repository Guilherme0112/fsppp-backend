<?php

namespace Core\Domain\SolicitacaoPagamento\Exceptions;

use RuntimeException;

class TransicaoStatusInvalidaException extends RuntimeException
{
    public function __construct(string $mensagem)
    {
        parent::__construct($mensagem);
    }
}
