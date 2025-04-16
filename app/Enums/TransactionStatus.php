<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case PENDING = 'PENDING';
    case COMPLETED = 'COMPLETED';
    case CANCELED = 'CANCELED';

    public function labels(): string
    {
        return match ($this) {
            self::PENDING => 'PENDING',
            self::COMPLETED => 'COMPLETED',
            self::CANCELED => 'CANCELED',
        };
    }
}
