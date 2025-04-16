<?php

namespace App\Exceptions\Business;

use Symfony\Component\HttpFoundation\Response;

class ExternalAuthorizationFailedException extends BusinessException
{
    public function __construct()
    {
        parent::__construct('Authorization failed', Response::HTTP_BAD_REQUEST);
    }
}
