<?php

namespace App\Enums;

enum AccountType : string
{
    case PAYMENT = 'PAYMENT';
    public function labels(): string
    {
        return match ($this) {
            self::PAYMENT => 'PAYMENT'
        };
    }
}
