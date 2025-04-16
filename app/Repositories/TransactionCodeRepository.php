<?php

namespace App\Repositories;

use App\Enums\TransactionType;
use App\Models\TransactionCode;

class TransactionCodeRepository
{
    public function findById(int $id): ?TransactionCode
    {
        return TransactionCode::where('id', $id)
            ->first();
    }
}
