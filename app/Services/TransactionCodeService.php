<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Exceptions\Business\TransactionCodeNotFoundException;
use App\Models\TransactionCode;
use App\Repositories\TransactionCodeRepository;

class TransactionCodeService
{
    private TransactionCodeRepository $repository;

    public function __construct(TransactionCodeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Find a transaction code by its ID.
     *
     * @param integer $id The ID of the transaction code to find.
     *
     * @return TransactionCode|null The found transaction code or null if not found.
     *
     * @throws TransactionCodeNotFoundException When the transaction code is not found.
     */
    public function findById(int $id): ?TransactionCode
    {
        $transactionCode = $this->repository->findById($id);

        if (!$transactionCode) {
            throw new TransactionCodeNotFoundException();
        }

        return $transactionCode;
    }
}
