<?php

namespace App\Services;

use App\Enums\TransactionCode;
use Exception;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class TransferService
{
    private AccountService $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    /**
     * Transfer amount between accounts.
     *
     * @param integer $accountOrigin      The ID of the origin account.
     * @param integer $accountDestination The ID of the destination account.
     * @param float   $amount             The amount to transfer.
     *
     * @return array An array containing both debit and credit transactions.
     *
     * @throws Exception For general exceptions.
     * @throws \Interop\Queue\Exception For queue-related exceptions.
     */
    public function transfer(
        int $accountOrigin,
        int $accountDestination,
        float $amount
    ): array {
        try {
            $correlationId = Uuid::uuid4()->toString();

            DB::beginTransaction();

            $debit = $this->accountService->debit(
                $accountOrigin,
                $amount,
                TransactionCode::TRANSFER_DEBIT,
                $correlationId
            );

            $credit = $this->accountService->credit(
                $accountDestination,
                $amount,
                TransactionCode::TRANSFER_CREDIT,
                null,
                $correlationId
            );

            DB::commit();

            return [$debit, $credit];
        } catch (Exception | Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
