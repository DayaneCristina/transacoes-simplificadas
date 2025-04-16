<?php

namespace Tests\Unit\Controllers;

use App\Enums\AccountType;
use App\Enums\TransactionCode;
use App\Http\Controllers\AccountController;
use App\Services\AccountService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use ValueError;

class AccountControllerTest extends TestCase
{
    private AccountService $accountService;
    private AccountController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->accountService = $this->createMock(AccountService::class);
        $this->controller = new AccountController($this->accountService);
    }

    public function testBalanceSuccessfully()
    {
        $userId = 123;
        $accountType = AccountType::PAYMENT;
        $expectedBalance = 1000.50;

        $request = new Request();
        $request->headers->set('user-id', $userId);
        $request->query->set('account_type', $accountType->value);

        $this->accountService->expects($this->once())
            ->method('getAccountBalance')
            ->with($userId, $accountType)
            ->willReturn($expectedBalance);

        $response = $this->controller->balance($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['balance' => $expectedBalance], $response->getData(true));
    }

    public function testCreditSuccessfully()
    {
        $userId = 123;
        $destination = 456;
        $amount = 100.50;
        $transactionCode = TransactionCode::MANUAL_CREDIT;

        $request = new Request([
            'amount' => $amount,
            'account_destination' => $destination,
            'transaction_code' => $transactionCode->value,
        ]);
        $request->headers->set('user-id', $userId);

        $this->accountService->expects($this->once())
            ->method('credit')
            ->with($destination, $amount, $transactionCode, $userId);

        $response = $this->controller->credit($request);

        $this->assertEquals(204, $response->getStatusCode());
    }

    public function testCreditThrowsValidationException()
    {
        $this->expectException(ValidationException::class);

        $request = new Request([
            'amount' => 'invalid', // should be numeric
            'account_destination' => 456,
            'transaction_code' => TransactionCode::MANUAL_CREDIT->value,
        ]);
        $request->headers->set('user-id', 123);

        $this->controller->credit($request);
    }

    public function testCreditThrowsExceptionWhenTransactionCodeIsInvalid()
    {
        $this->expectException(ValueError::class);

        $invalidTransactionCode = 9999; // non-existent code

        $request = new Request([
            'amount' => 100.50,
            'account_destination' => 456,
            'transaction_code' => $invalidTransactionCode,
        ]);
        $request->headers->set('user-id', 123);

        $this->controller->credit($request);
    }
}
