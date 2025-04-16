<?php

namespace App\Exceptions\Business;

use Symfony\Component\HttpFoundation\Response;

class InsufficientBalanceException extends BusinessException
{
    public function __construct()
    {
        parent::__construct('Insufficient balance', Response::HTTP_BAD_REQUEST);
    }
}
