<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountsTableSeeder extends Seeder
{
    public function run() : void
    {
        for ($i = 1; $i <= 4; $i++) {
            Account::factory()->create([
                'id'      => $i,
                'name'    => 'PAYMENT',
                'user_id' => $i
            ]);
        }
    }
}
