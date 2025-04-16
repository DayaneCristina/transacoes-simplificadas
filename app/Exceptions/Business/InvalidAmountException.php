<?php

namespace App\Exceptions\Business;

use Symfony\Component\HttpFoundation\Response;

class InvalidAmountException extends BusinessException
{
    public function __construct()
    {
        parent::__construct('Invalid amount', Response::HTTP_BAD_REQUEST);
    }
}
