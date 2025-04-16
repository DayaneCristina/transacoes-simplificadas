<?php

namespace Tests\Unit\Services;

use App\Enums\AccountType;
use App\Enums\TransactionCode;
use App\Enums\UserType;
use App\Exceptions\Business\AccountNotFoundException;
use App\Exceptions\Business\CreditNotAllowedException;
use App\Exceptions\Business\DebitNotAllowedException;
use App\Exceptions\Business\ExternalAuthorizationFailedException;
use App\Exceptions\Business\InsufficientBalanceException;
use App\Exceptions\Business\InvalidAmountException;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use App\Repositories\AccountRepository;
use App\Services\AccountService;
use App\Services\AuthorizerService;
use App\Services\KafkaService;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AccountServiceTest extends TestCase
{
    use RefreshDatabase;

    private AccountService $service;
    private AccountRepository $accountRepository;
    private TransactionService $transactionService;
    private AuthorizerService $authorizerService;
    private KafkaService $kafkaService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->accountRepository = $this->createMock(AccountRepository::class);
        $this->transactionService = $this->createMock(TransactionService::class);
        $this->authorizerService = $this->createMock(AuthorizerService::class);
        $this->kafkaService = $this->createMock(KafkaService::class);

        $this->service = new AccountService(
            $this->accountRepository,
            $this->transactionService,
            $this->authorizerService,
            $this->kafkaService
        );
    }

    public function testGetAccountBalanceWithAccountType()
    {
        $userId = 1;
        $accountType = AccountType::PAYMENT;
        $expectedBalance = 1000.50;

        $this->accountRepository->expects($this->once())
            ->method('getBalanceByUserIdAndAccountName')
            ->with($userId, $accountType->value)
            ->willReturn($expectedBalance);

        $result = $this->service->getAccountBalance($userId, $accountType);

        $this->assertEquals(round($expectedBalance, 2), $result);
    }

    public function testGetAccountBalanceWithoutAccountType()
    {
        $userId = 1;
        $expectedBalance = 1500.75;

        $this->accountRepository->expects($this->once())
            ->method('getBalanceByUserId')
            ->with($userId)
            ->willReturn($expectedBalance);

        $result = $this->service->getAccountBalance($userId);

        $this->assertEquals(round($expectedBalance, 2), $result);
    }

    public function testDebitSuccessfully()
    {
        $accountId = 1;
        $amount = 100.50;
        $transactionCode = TransactionCode::MANUAL_CREDIT;
        $correlationId = 'test-correlation-id';
        $balance = 200.00;

        $user = User::factory()->create(['type' => UserType::CUSTOMER->value]);
        $account = Account::factory()->create(['user_id' => $user->id, 'name' => AccountType::PAYMENT->value]);

        $transaction = new Transaction();

        $this->accountRepository->expects($this->once())
            ->method('findById')
            ->with($accountId)
            ->willReturn($account);

        $this->accountRepository->expects($this->once())
            ->method('getBalanceByUserIdAndAccountName')
            ->with($user->id, $account->name)
            ->willReturn($balance);

        $this->transactionService->expects($this->once())
            ->method('create')
            ->with($account, $transactionCode, $amount, $correlationId)
            ->willReturn($transaction);

        $this->transactionService->expects($this->once())
            ->method('complete')
            ->with($transaction);

        $result = $this->service->debit($accountId, $amount, $transactionCode, $correlationId);

        $this->assertInstanceOf(Transaction::class, $result);
    }

    public function testDebitThrowsInvalidAmountException()
    {
        $this->expectException(InvalidAmountException::class);

        $this->service->debit(1, 0, TransactionCode::TRANSFER_DEBIT, 'test-correlation-id');
    }

    public function testDebitThrowsAccountNotFoundException()
    {
        $this->expectException(AccountNotFoundException::class);

        $this->accountRepository->expects($this->once())
            ->method('findById')
            ->willReturn(null);

        $this->service->debit(1, 100, TransactionCode::TRANSFER_DEBIT, 'test-correlation-id');
    }

    public function testDebitThrowsDebitNotAllowedForSeller()
    {
        $this->expectException(DebitNotAllowedException::class);

        $user = User::factory()->create(['type' => UserType::SELLER->value]);
        $account = Account::factory()->create(['user_id' => $user->id]);

        $this->accountRepository->expects($this->once())
            ->method('findById')
            ->willReturn($account);

        $this->service->debit(1, 100, TransactionCode::TRANSFER_DEBIT, 'test-correlation-id');
    }

    public function testDebitThrowsInsufficientBalanceException()
    {
        $this->expectException(InsufficientBalanceException::class);

        $user = User::factory()->create(['type' => UserType::CUSTOMER->value]);
        $account = Account::factory()->create(['user_id' => $user->id]);

        $this->accountRepository->expects($this->once())
            ->method('findById')
            ->willReturn($account);

        $this->accountRepository->expects($this->once())
            ->method('getBalanceByUserIdAndAccountName')
            ->willReturn(50.00); // Less than the debit amount

        $this->service->debit(1, 100, TransactionCode::TRANSFER_DEBIT, 'test-correlation-id');
    }

    public function testCreditSuccessfully()
    {
        $accountId = 1;
        $amount = 100.50;
        $transactionCode = TransactionCode::TRANSFER_CREDIT;
        $correlationId = 'test-correlation-id';

        $account = Account::factory()->create();
        $transaction = new Transaction();

        $this->accountRepository->expects($this->once())
            ->method('findById')
            ->with($accountId)
            ->willReturn($account);

        $this->authorizerService->expects($this->once())
            ->method('authorize');

        $this->transactionService->expects($this->once())
            ->method('create')
            ->with($account, $transactionCode, $amount, $correlationId)
            ->willReturn($transaction);

        $this->transactionService->expects($this->once())
            ->method('complete')
            ->with($transaction);

        $this->kafkaService->expects($this->once())
            ->method('produce');

        $result = $this->service->credit($accountId, $amount, $transactionCode, null, $correlationId);

        $this->assertInstanceOf(Transaction::class, $result);
    }

    public function testCreditThrowsInvalidAmountException()
    {
        $this->expectException(InvalidAmountException::class);

        $this->service->credit(1, 0, TransactionCode::TRANSFER_CREDIT);
    }

    public function testCreditThrowsCreditNotAllowedException()
    {
        $user = User::factory()->create(['type' => UserType::CUSTOMER->value]);
        $this->expectException(CreditNotAllowedException::class);

        $account = Account::factory()->create(['user_id' => $user->id]);

        $this->accountRepository->expects($this->once())
            ->method('findById')
            ->willReturn($account);

        $this->service->credit(1, 100, TransactionCode::TRANSFER_CREDIT, 4);
    }

    public function testCreditThrowsExternalAuthorizationFailedException()
    {
        $this->expectException(ExternalAuthorizationFailedException::class);

        $account = Account::factory()->create();

        $this->accountRepository->expects($this->once())
            ->method('findById')
            ->willReturn($account);

        $this->authorizerService->expects($this->once())
            ->method('authorize')
            ->willThrowException(new ExternalAuthorizationFailedException());

        $this->service->credit(1, 100, TransactionCode::TRANSFER_CREDIT);
    }
}
