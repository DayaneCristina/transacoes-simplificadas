<?php

namespace App\Repositories;

use App\Enums\TransactionStatus;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionCode;

class TransactionRepository
{
    public function create(
        Account $account,
        TransactionCode $transactionCode,
        float $amount,
        string $correlationId
    ): Transaction {
        $transaction = Transaction::create([
            'status'              => TransactionStatus::PENDING->value,
            'amount'              => $amount,
            'correlation_id'      => $correlationId,
            'account_id'          => $account->id,
            'transaction_code_id' => $transactionCode->id,
        ]);

        return $transaction;
    }

    public function complete(Transaction $transaction): void
    {
        $transaction->status = TransactionStatus::COMPLETED->value;
        $transaction->save();
        $transaction->refresh();
    }

    public function cancel(Transaction $transaction): void
    {
        $transaction->status = TransactionStatus::CANCELED->value;
        $transaction->save();
        $transaction->refresh();
    }
}
