<?php

namespace App\Exceptions\Business;

use Symfony\Component\HttpFoundation\Response;

class NotificationFailedException extends BusinessException
{
    public function __construct()
    {
        parent::__construct('Notification failed', Response::HTTP_BAD_REQUEST);
    }
}
