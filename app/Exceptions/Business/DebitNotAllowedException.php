<?php

namespace App\Exceptions\Business;

use Symfony\Component\HttpFoundation\Response;

class DebitNotAllowedException extends BusinessException
{
    public function __construct()
    {
        parent::__construct('Debit not allowed', Response::HTTP_FORBIDDEN);
    }
}
