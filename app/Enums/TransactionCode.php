<?php

namespace App\Enums;

enum TransactionCode : int
{
    case TRANSFER_CREDIT = 1;
    case TRANSFER_DEBIT = 2;
    case MANUAL_CREDIT = 3;

    public function labels(): string
    {
        return match ($this) {
            self::TRANSFER_DEBIT => 'Débito para transferência',
            self::TRANSFER_CREDIT => 'Crédito de transferência',
            self::MANUAL_CREDIT => 'Crédito manual em conta',
        };
    }
}
