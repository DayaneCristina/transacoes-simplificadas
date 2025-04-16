<?php

namespace Database\Factories;

use App\Enums\TransactionStatus;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionCode;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{

    public function definition(): array
    {
        return [
            'account_id' => function () {
                return Account::factory()->create()->id;
            },
            'transaction_code_id' => function () {
                return TransactionCode::factory()->create()->id;
            },
            'amount' => fake()->randomFloat(2, 1, 1000),
            'status' => TransactionStatus::COMPLETED->value,
            'correlation_id' => fake()->uuid(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
