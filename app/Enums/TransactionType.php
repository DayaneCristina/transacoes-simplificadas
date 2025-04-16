<?php

namespace App\Enums;

enum TransactionType : string
{
    case DEBIT = 'DEBIT';
    case CREDIT = 'CREDIT';

    public function labels(): string
    {
        return match ($this) {
            self::DEBIT => 'DEBIT',
            self::CREDIT => 'CREDIT',
        };
    }
}
