<?php

namespace App\Exceptions\Business;

use Symfony\Component\HttpFoundation\Response;

class CreditNotAllowedException extends BusinessException
{
    public function __construct()
    {
        parent::__construct('Credit not allowed', Response::HTTP_FORBIDDEN);
    }
}
