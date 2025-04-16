<?php

namespace Controllers;

use App\Exceptions\Business\AccountNotFoundException;
use App\Http\Controllers\TransferController;
use App\Services\TransferService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class TransferControllerTest extends TestCase
{
    private TransferService $transferService;
    private TransferController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transferService = $this->createMock(TransferService::class);
        $this->controller = new TransferController($this->transferService);
    }
    public function testTransferSuccessfully()
    {
        $origin = 123;
        $destination = 456;
        $amount = 100.50;

        $request = new Request([
            'amount' => $amount,
            'account_origin' => $origin,
            'account_destination' => $destination,
        ]);

        $this->transferService->expects($this->once())
            ->method('transfer')
            ->with($origin, $destination, $amount);

        $response = $this->controller->transfer($request);

        $this->assertEquals(204, $response->getStatusCode());
    }

    public function testTransferThrowsValidationExceptionWhenAccountsSame()
    {
        $this->expectException(ValidationException::class);

        $origin = 123;
        $amount = 100.50;

        $request = new Request([
            'amount' => $amount,
            'account_origin' => $origin,
            'account_destination' => $origin, // same as origin
        ]);

        $this->controller->transfer($request);
    }

    public function testTransferThrowsValidationExceptionWhenAmountInvalid()
    {
        $this->expectException(ValidationException::class);

        $request = new Request([
            'amount' => 'invalid', // should be numeric
            'account_origin' => 123,
            'account_destination' => 456,
        ]);

        $this->controller->transfer($request);
    }

    public function testTransferThrowsExceptionWhenAccountNotFound()
    {
        $this->expectException(AccountNotFoundException::class);

        $origin = 123;
        $destination = 456;
        $amount = 100.50;

        $request = new Request([
            'amount' => $amount,
            'account_origin' => $origin,
            'account_destination' => $destination,
        ]);

        $this->transferService->expects($this->once())
            ->method('transfer')
            ->willThrowException(new AccountNotFoundException());

        $this->controller->transfer($request);
    }
}
