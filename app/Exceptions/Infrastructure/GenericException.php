<?php

namespace App\Exceptions\Infrastructure;

class GenericException extends \Exception
{
    public function __construct()
    {
        parent::__construct("Internal Server Error", 500);
    }
}
