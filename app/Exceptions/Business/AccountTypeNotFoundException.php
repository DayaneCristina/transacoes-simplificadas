<?php

namespace App\Exceptions\Business;

use Symfony\Component\HttpFoundation\Response;

class AccountTypeNotFoundException extends BusinessException
{
    public function __construct()
    {
        parent::__construct('Account type not found', Response::HTTP_BAD_REQUEST);
    }
}
