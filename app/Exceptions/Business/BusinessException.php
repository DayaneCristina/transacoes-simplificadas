<?php

namespace App\Exceptions\Business;

class BusinessException extends \Exception
{
    public function __construct($message, $code)
    {
        parent::__construct($message, $code);
    }
}
