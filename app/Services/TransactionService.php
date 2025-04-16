<?php

namespace App\Services;

use App\Enums\TransactionCode;
use App\Enums\TransactionType;
use App\Enums\UserType;
use App\Exceptions\Business\DebitNotAllowedException;
use App\Exceptions\Business\TransactionCodeNotFoundException;
use App\Models\Account;
use App\Models\Transaction;
use App\Repositories\TransactionRepository;

class TransactionService
{
    private readonly TransactionRepository $repository;
    private readonly TransactionCodeService $transactionCodeService;

    public function __construct(
        TransactionRepository $repository,
        TransactionCodeService $transactionCodeService
    ) {
        $this->repository = $repository;
        $this->transactionCodeService = $transactionCodeService;
    }

    /**
     * Create a new transaction record.
     *
     * @param Account         $account             The account associated with the transaction.
     * @param TransactionCode $transactionCodeEnum The transaction code enum.
     * @param float           $amount              The transaction amount.
     * @param string          $correlationId       The correlation ID for tracking.
     *
     * @return Transaction The created transaction record.
     *
     * @throws TransactionCodeNotFoundException When the transaction code is not found.
     */
    public function create(
        Account $account,
        TransactionCode $transactionCodeEnum,
        float $amount,
        string $correlationId
    ): Transaction {
        $transactionCode = $this->transactionCodeService->findById($transactionCodeEnum->value);

        $amount = $transactionCode->type == TransactionType::DEBIT->value ? $amount * -1 : $amount;

        return $this->repository->create(
            account: $account,
            transactionCode: $transactionCode,
            amount: $amount,
            correlationId: $correlationId
        );
    }

    public function complete(Transaction $transaction): void
    {
        $this->repository->complete($transaction);
    }

    public function cancel(Transaction $transaction): void
    {
        $this->repository->cancel($transaction);
    }
}
