<?php

namespace Tests\Unit\Services;

use App\Enums\TransactionCode;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Enums\UserType;
use App\Exceptions\Business\DebitNotAllowedException;
use App\Exceptions\Business\TransactionCodeNotFoundException;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionCode as TransactionCodeModel;
use App\Models\User;
use App\Repositories\TransactionRepository;
use App\Services\TransactionCodeService;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    private TransactionService $service;
    private TransactionRepository $repositoryMock;
    private TransactionCodeService $transactionCodeServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositoryMock = $this->createMock(TransactionRepository::class);
        $this->transactionCodeServiceMock = $this->createMock(TransactionCodeService::class);

        $this->service = new TransactionService(
            $this->repositoryMock,
            $this->transactionCodeServiceMock
        );
    }

    public function testCreateDebitTransaction()
    {
        $transactionType = TransactionType::DEBIT;
        $transactionCode = TransactionCode::TRANSFER_DEBIT;

        $transactionCodeModel = TransactionCodeModel::factory()->create([
            'id' => $transactionCode->value,
            'type' => $transactionType->value
        ]);
        $transaction = Transaction::factory()->create(['transaction_code_id' => $transactionCodeModel->id]);
        $user = User::factory()->create(['type' => UserType::CUSTOMER->value]);
        $account = Account::factory()->create(['user_id' => $user->id]);

        $amount = 100.50;
        $correlationId = 'test-correlation-id';

        $this->transactionCodeServiceMock->expects($this->once())
            ->method('findById')
            ->with($transactionCodeModel->id)
            ->willReturn($transactionCodeModel);

        $this->repositoryMock->expects($this->once())
            ->method('create')
            ->willReturn($transaction);

        $result = $this->service->create($account, $transactionCode, $amount, $correlationId);

        $this->assertSame($transaction, $result);
    }

    public function testCreateCreditTransaction()
    {
        $transactionCode = TransactionCode::MANUAL_CREDIT;

        $transactionCodeModel = TransactionCodeModel::factory()->create([
            'id' => $transactionCode->value,
            'type' => TransactionType::CREDIT->value
        ]);
        $transaction = Transaction::factory()->create(['transaction_code_id' => $transactionCodeModel->id]);
        $user = User::factory()->create(['type' => UserType::CUSTOMER->value]);
        $account = Account::factory()->create(['user_id' => $user->id]);

        $amount = 100.50;
        $correlationId = 'test-correlation-id';

        $this->transactionCodeServiceMock->expects($this->once())
            ->method('findById')
            ->with($transactionCodeModel->id)
            ->willReturn($transactionCodeModel);

        $this->repositoryMock->expects($this->once())
            ->method('create')
            ->willReturn($transaction);

        $result = $this->service->create($account, $transactionCode, $amount, $correlationId);

        $this->assertSame($transaction, $result);
    }

    public function testCompleteTransaction()
    {
        $transaction = Transaction::factory()->create(['status' => TransactionStatus::COMPLETED->value]);

        $this->repositoryMock->expects($this->once())
            ->method('complete')
            ->with($transaction);

        $this->service->complete($transaction);
    }

    public function testCancelTransaction()
    {
        $transaction = Transaction::factory()->create(['status' => TransactionStatus::CANCELED->value]);;

        $this->repositoryMock->expects($this->once())
            ->method('cancel')
            ->with($transaction);

        $this->service->cancel($transaction);
    }
}
