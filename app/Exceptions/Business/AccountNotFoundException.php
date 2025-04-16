<?php

namespace App\Exceptions\Business;

use Symfony\Component\HttpFoundation\Response;

class AccountNotFoundException extends BusinessException
{
    public function __construct()
    {
        parent::__construct('Account not found', Response::HTTP_BAD_REQUEST);
    }
}
