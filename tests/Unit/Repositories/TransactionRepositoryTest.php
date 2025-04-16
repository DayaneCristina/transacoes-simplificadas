<?php

namespace Tests\Unit\Repositories;

use App\Enums\TransactionStatus;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionCode;
use App\Repositories\TransactionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private TransactionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new TransactionRepository();
    }

    public function testCreateTransactionSuccessfully()
    {
        $account = Account::factory()->create();
        $transactionCode = TransactionCode::factory()->create();
        $amount = 100.50;
        $correlationId = 'test-correlation-id';

        $transaction = $this->repository->create($account, $transactionCode, $amount, $correlationId);

        $this->assertEquals($account->id, $transaction->account_id);
        $this->assertEquals($transactionCode->id, $transaction->transaction_code_id);
        $this->assertEquals($amount, $transaction->amount);
        $this->assertEquals($correlationId, $transaction->correlation_id);
        $this->assertEquals(TransactionStatus::PENDING->value, $transaction->status);
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'account_id' => $account->id,
            'amount' => $amount,
            'correlation_id' => $correlationId
        ]);
    }

    public function testCompleteTransactionSuccessfully()
    {
        $transaction = Transaction::factory()->create([
            'status' => TransactionStatus::PENDING->value
        ]);

        $this->repository->complete($transaction);

        $transaction->refresh();
        $this->assertEquals(TransactionStatus::COMPLETED->value, $transaction->status);
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => TransactionStatus::COMPLETED->value
        ]);
    }

    public function testCancelTransactionSuccessfully()
    {
        $transaction = Transaction::factory()->create([
            'status' => TransactionStatus::PENDING->value
        ]);

        $this->repository->cancel($transaction);

        $transaction->refresh();
        $this->assertEquals(TransactionStatus::CANCELED->value, $transaction->status);
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => TransactionStatus::CANCELED->value
        ]);
    }

    public function testCreateTransactionWithNegativeAmount()
    {
        $account = Account::factory()->create();
        $transactionCode = TransactionCode::factory()->create();
        $amount = -100.50; // Negative amount
        $correlationId = 'test-negative-amount';

        $transaction = $this->repository->create($account, $transactionCode, $amount, $correlationId);

        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertEquals($amount, $transaction->amount);
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'amount' => $amount
        ]);
    }

    public function testCompleteAlreadyCompletedTransaction()
    {
        $transaction = Transaction::factory()->create([
            'status' => TransactionStatus::COMPLETED->value
        ]);

        $this->repository->complete($transaction);

        $transaction->refresh();
        $this->assertEquals(TransactionStatus::COMPLETED->value, $transaction->status);
    }
}
