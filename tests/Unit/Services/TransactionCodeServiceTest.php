<?php

namespace Tests\Unit\Services;

use App\Exceptions\Business\TransactionCodeNotFoundException;
use App\Models\TransactionCode;
use App\Repositories\TransactionCodeRepository;
use App\Services\TransactionCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionCodeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TransactionCodeRepository $repositoryMock;
    protected TransactionCodeService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositoryMock = $this->createMock(TransactionCodeRepository::class);
        $this->service = new TransactionCodeService($this->repositoryMock);
    }

    public function testFindByIdReturnsTransactionCode(): void
    {
        $transactionCode = TransactionCode::factory()->create();

        $this->repositoryMock->expects($this->once())
            ->method('findById')
            ->with()
            ->willReturn($transactionCode);
        $result = $this->service->findById($transactionCode->id);

        $this->assertSame($transactionCode, $result);
    }

    public function testFindByIdThrowsExceptionWhenNotFound(): void
    {
        $id = 999;

        $this->repositoryMock->expects($this->once())
            ->method('findById')
            ->with()
            ->willReturn(null);

        $this->expectException(TransactionCodeNotFoundException::class);
        $this->service->findById($id);
    }
}
