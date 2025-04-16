<?php

namespace App\Exceptions\Business;

use Symfony\Component\HttpFoundation\Response;

class TransactionCodeNotFoundException extends BusinessException
{
    public function __construct()
    {
        parent::__construct('Transaction Type not found', Response::HTTP_BAD_REQUEST);
    }
}
