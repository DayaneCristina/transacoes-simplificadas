<?php

namespace App\Enums;

enum UserType : string
{
    case CUSTOMER = 'CUSTOMER';
    case SELLER = 'SELLER';

    public function labels(): string
    {
        return match ($this) {
            self::CUSTOMER => 'CUSTOMER',
            self::SELLER => 'SELLER',
        };
    }
}
