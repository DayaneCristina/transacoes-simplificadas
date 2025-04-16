<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransferController;
use App\Http\Middlewares\ValidateUserIdOnHeader;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json(['message' => 'Health']);
});

Route::prefix('accounts')->middleware(ValidateUserIdOnHeader::class)
    ->group(function () {
        Route::get('/balance', [AccountController::class, 'balance']);

        Route::post('/credit', [AccountController::class, 'credit']);
    });

Route::prefix('transfer')->middleware(ValidateUserIdOnHeader::class)
    ->group(function () {
        Route::post('/', [TransferController::class, 'transfer']);
    });
