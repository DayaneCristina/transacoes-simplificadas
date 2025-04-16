<?php

namespace Tests\Unit\Repositories;

use App\Enums\AccountType;
use App\Enums\TransactionStatus;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use App\Repositories\AccountRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private AccountRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new AccountRepository();
    }

    public function testFindByIdReturnsAccountWhenExists()
    {
        $account = Account::factory()->create();

        $result = $this->repository->findById($account->id);

        $this->assertInstanceOf(Account::class, $result);
        $this->assertEquals($account->id, $result->id);
    }

    public function testFindByIdReturnsNullWhenNotExists()
    {
        $result = $this->repository->findById(9999); // Non-existent ID

        $this->assertNull($result);
    }

    public function testGetBalanceByUserIdCalculatesCorrectBalance()
    {
        $user = User::factory()->create();
        $account1 = Account::factory()->create(['user_id' => $user->id]);

        Transaction::factory()->create([
            'account_id' => $account1->id,
            'amount' => 100.50,
            'status' => TransactionStatus::COMPLETED
        ]);
        Transaction::factory()->create([
            'account_id' => $account1->id,
            'amount' => 50.25,
            'status' => TransactionStatus::COMPLETED
        ]);

        $expectedBalance = 100.50 + 50.25;
        $result = $this->repository->getBalanceByUserId($user->id);

        $this->assertEquals($expectedBalance, $result);
    }

    public function testGetBalanceByUserIdReturnsZeroWhenNoTransactions()
    {
        $user = User::factory()->create();
        Account::factory()->create(['user_id' => $user->id]);

        $result = $this->repository->getBalanceByUserId($user->id);

        $this->assertEquals(0, $result);
    }

    public function testGetBalanceByUserIdAndAccountNameCalculatesCorrectBalance()
    {
        $user = User::factory()->create();
        $account = Account::factory()->create([
            'user_id' => $user->id,
            'name' => 'PAYMENT'
        ]);

        Transaction::factory()->create([
            'account_id' => $account->id,
            'amount' => 150.50,
            'status' => TransactionStatus::COMPLETED
        ]);
        Transaction::factory()->create([
            'account_id' => $account->id,
            'amount' => 75.25,
            'status' => TransactionStatus::COMPLETED
        ]);

        $expectedBalance = 150.50 + 75.25;
        $result = $this->repository->getBalanceByUserIdAndAccountName($user->id, AccountType::PAYMENT->value);

        $this->assertEquals($expectedBalance, $result);
    }

    public function testGetBalanceByUserIdAndAccountNameReturnsZeroWhenNoMatch()
    {
        $user = User::factory()->create();
        Account::factory()->create([
            'user_id' => $user->id,
            'name' => 'PAYMENT'
        ]);

        $result = $this->repository->getBalanceByUserIdAndAccountName($user->id, AccountType::PAYMENT->value);

        $this->assertEquals(0, $result);
    }
}
