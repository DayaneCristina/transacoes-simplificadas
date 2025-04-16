<?php

namespace Services;

use App\Enums\UserType;
use App\Models\Transaction;
use App\Models\User;
use App\Services\AccountService;
use App\Services\TransferService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TransferServiceTest extends TestCase
{
    use RefreshDatabase;

    private TransferService $service;
    private AccountService $accountService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->accountService = $this->createMock(AccountService::class);

        $this->service = new TransferService($this->accountService);
    }

    public function testTransferSuccessfully()
    {
        $accountOrigin = 1;
        $accountDestination = 2;
        $amount = 100.50;

        $debitTransaction = new Transaction();
        $creditTransaction = new Transaction();

        $this->accountService->expects($this->once())
            ->method('debit')
            ->willReturn($debitTransaction);

        $this->accountService->expects($this->once())
            ->method('credit')
            ->willReturn($creditTransaction);

        $result = $this->service->transfer($accountOrigin, $accountDestination, $amount);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(Transaction::class, $result[0]);
        $this->assertInstanceOf(Transaction::class, $result[1]);
    }

    public function testTransferRollsBackOnFailure()
    {
        $this->expectException(\Exception::class);

        $accountOrigin = 1;
        $accountDestination = 2;
        $amount = 100.50;

        $this->accountService->expects($this->once())
            ->method('debit')
            ->willThrowException(new \Exception());

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('rollBack')->once();

        $this->service->transfer($accountOrigin, $accountDestination, $amount);
    }
}
