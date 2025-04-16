<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransactionCode>
 */
class TransactionCodeFactory extends Factory
{

    public function definition(): array
    {
        return [
            'title'       => $this->faker->randomElement(['Transferência Recebida', 'Transferência Realizada']),
            'type'        => $this->faker->randomElement(['DEBIT', 'CREDIT']),
            'description' => $this->faker->text(),
        ];
    }
}
