<?php

namespace App\Exceptions\Business;

use Symfony\Component\HttpFoundation\Response;

class ExternalAuthorizationNotAllowedException extends BusinessException
{
    public function __construct()
    {
        parent::__construct('Authorization denied', Response::HTTP_FORBIDDEN);
    }
}
