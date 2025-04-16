<?php

namespace App\Exceptions\Business;

use Symfony\Component\HttpFoundation\Response;

class UserIdNotProvidedException extends BusinessException
{
    public function __construct()
    {
        parent::__construct('user-id must be provided on header', Response::HTTP_BAD_REQUEST);
    }
}
