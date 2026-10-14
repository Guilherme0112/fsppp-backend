<?php

namespace Core\Domain\SolicitacaoPagamento\Exceptions;

use RuntimeException;

class SolicitacaoNaoEditavelException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Esta solicitação já foi analisada e não pode mais ser editada.');
    }
}
