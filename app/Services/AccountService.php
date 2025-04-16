<?php

namespace App\Services;

use App\Enums\AccountType;
use App\Enums\TransactionCode;
use App\Enums\UserType;
use App\Exceptions\Business\AccountNotFoundException;
use App\Exceptions\Business\CreditNotAllowedException;
use App\Exceptions\Business\DebitNotAllowedException;
use App\Exceptions\Business\ExternalAuthorizationFailedException;
use App\Exceptions\Business\ExternalAuthorizationNotAllowedException;
use App\Exceptions\Business\InsufficientBalanceException;
use App\Exceptions\Business\InvalidAmountException;
use App\Exceptions\Business\TransactionCodeNotFoundException;
use App\Models\Account;
use App\Models\Transaction;
use App\Repositories\AccountRepository;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Interop\Queue\Exception\InvalidDestinationException;
use Interop\Queue\Exception\InvalidMessageException;
use Ramsey\Uuid\Uuid;
use Throwable;

class AccountService
{
    private AccountRepository $repository;
    private TransactionService $transactionService;
    private KafkaService $kafkaService;
    private AuthorizerService $authorizerService;

    public function __construct(
        AccountRepository $repository,
        TransactionService $transactionService,
        AuthorizerService $authorizerService,
        KafkaService $kafkaService
    ) {
        $this->repository = $repository;
        $this->transactionService = $transactionService;
        $this->authorizerService = $authorizerService;
        $this->kafkaService = $kafkaService;
    }

    /**
     * Debit an amount from an account.
     *
     * @param integer         $accountId       The ID of the account to debit.
     * @param float           $amount          The amount to debit.
     * @param TransactionCode $transactionCode The transaction code for the debit.
     * @param string          $correlationId   The correlation ID for tracking.
     *
     * @return Transaction The created transaction.
     *
     * @throws AccountNotFoundException When the account is not found.
     * @throws DebitNotAllowedException When debit is not allowed for the account type.
     * @throws InsufficientBalanceException When there's insufficient balance.
     * @throws InvalidAmountException When the amount is invalid.
     * @throws TransactionCodeNotFoundException When the transaction code is invalid.
     */
    public function debit(
        int $accountId,
        float $amount,
        TransactionCode $transactionCode,
        string $correlationId,
    ): Transaction {
        try {
            if ($amount <= 0) {
                throw new InvalidAmountException();
            }

            $account = $this->getAccountById($accountId);
            $user = $account->user()->get()->first();

            if ($user->type == UserType::SELLER->value) {
                throw new DebitNotAllowedException();
            }

            $balance = $this->repository->getBalanceByUserIdAndAccountName(
                $user->id,
                AccountType::tryFrom($account->name)->value
            );

            if ($balance < $amount) {
                throw new InsufficientBalanceException();
            }

            $transaction = $this->transactionService->create(
                $account,
                $transactionCode,
                $amount,
                $correlationId
            );
            $this->transactionService->complete($transaction);

            return $transaction;
        } catch (Exception $e) {
            if (isset($transaction) && $transaction) {
                $this->transactionService->cancel($transaction);
            }

            throw $e;
        }
    }

    /**
     * Credit an amount to an account.
     *
     * @param integer         $accountId       The ID of the account to credit.
     * @param float           $amount          The amount to credit.
     * @param TransactionCode $transactionCode The transaction code for the credit.
     * @param integer|null    $userId          The optional user ID for validation.
     * @param string|null     $correlationId   The optional correlation ID for tracking.
     *
     * @return Transaction The created transaction.
     *
     * @throws AccountNotFoundException When the account is not found.
     * @throws ConnectionException When connection to external service fails.
     * @throws CreditNotAllowedException When credit is not allowed.
     * @throws ExternalAuthorizationFailedException When external authorization fails.
     * @throws ExternalAuthorizationNotAllowedException When external authorization is not allowed.
     * @throws InvalidAmountException When the amount is invalid.
     * @throws InvalidDestinationException When the destination is invalid.
     * @throws InvalidMessageException When the message is invalid.
     * @throws TransactionCodeNotFoundException When the transaction code is invalid.
     * @throws \Interop\Queue\Exception For general queue-related exceptions.
     */
    public function credit(
        int $accountId,
        float $amount,
        TransactionCode $transactionCode,
        ?int $userId = null,
        ?string $correlationId = null,
    ): Transaction {
        try {
            if ($amount <= 0) {
                throw new InvalidAmountException();
            }

            $account = $this->getAccountById($accountId);

            if ($userId && ($account->user_id !== $userId)) { //phpcs:ignore
                throw new CreditNotAllowedException();
            }

            $correlationId = $correlationId ?? Uuid::uuid4()->toString();

            $this->authorizerService->authorize();

            $transaction = $this->transactionService->create(
                $account,
                $transactionCode,
                $amount,
                $correlationId
            );
            $this->transactionService->complete($transaction);

            $this->kafkaService->produce('account_notify_transaction', [
                    'event'          => 'CREDIT',
                    'transaction_id' => $transaction->id,
                    'correlation_id' => $transaction->correlationId
                ]);

            return $transaction;
        } catch (Exception | Throwable $e) {
            if (isset($transaction) && $transaction) {
                $this->transactionService->cancel($transaction);
            }

            throw $e;
        }
    }

    /**
     * Get account balance for a user.
     *
     * @param integer          $userId      The ID of the user.
     * @param AccountType|null $accountType The optional account type filter.
     *
     * @return float The account balance.
     */
    public function getAccountBalance(int $userId, ?AccountType $accountType = null): float
    {
        if ($accountType) {
            return round($this->repository->getBalanceByUserIdAndAccountName($userId, $accountType->value), 2);
        }

        return round($this->repository->getBalanceByUserId($userId), 2);
    }

    /**
     * Get account by ID.
     *
     * @param integer $accountId The ID of the account.
     *
     * @return Account The found account.
     *
     * @throws AccountNotFoundException When the account is not found.
     */
    private function getAccountById(int $accountId): Account
    {
        $account = $this->repository->findById($accountId);

        if (!$account) {
            throw new AccountNotFoundException();
        }

        return $account;
    }
}
