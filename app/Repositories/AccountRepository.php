<?php

namespace App\Repositories;

use App\Models\Account;

class AccountRepository
{
    public function findById(int $id): ?Account
    {
        return Account::where('id', $id)
            ->first();
    }

    public function getBalanceByUserId(int $userId): float
    {
        return Account::where('user_id', $userId)
            ->whereHas('transactions', fn($q) => $q->completed())
            ->with(['transactions' => fn($q) => $q->completed()])
            ->get()
            ->sum(fn($account) => $account->transactions->sum('amount'));
    }

    public function getBalanceByUserIdAndAccountName(int $userId, string $accountName): float
    {
        return Account::where('id', $userId)
            ->where('name', $accountName)
            ->with(['transactions' => fn($q) => $q->completed()])
            ->get()->sum(fn($account) => $account->transactions->sum('amount'));
    }
}
