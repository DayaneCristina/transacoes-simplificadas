<?php

namespace Tests\Unit\Repositories;

use App\Enums\TransactionType;
use App\Models\TransactionCode;
use App\Repositories\TransactionCodeRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionCodeRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private TransactionCodeRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new TransactionCodeRepository();
    }

    public function testFindByIdReturnsCodeWhenExists()
    {
        $code = TransactionCode::factory()->create();

        $result = $this->repository->findById($code->id);

        $this->assertInstanceOf(TransactionCode::class, $result);
        $this->assertEquals($code->id, $result->id);
    }

    public function testFindByIdReturnsNullWhenNotExists()
    {
        $result = $this->repository->findById(9999);

        $this->assertNull($result);
    }
}
