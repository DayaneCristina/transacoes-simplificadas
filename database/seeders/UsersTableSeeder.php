<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'id'       => 1,
                'name'     => 'Teste Customer 01',
                'email'    => 'teste_customer_01@email.com',
                'password' => Hash::make('123456'),
                'type'     => 'CUSTOMER',
                'document' => '22450717005'
            ],
            [
                'id'       => 2,
                'name'     => 'Teste Customer 02',
                'email'    => 'teste_customer_02@email.com',
                'password' => Hash::make('123456'),
                'type'     => 'CUSTOMER',
                'document' => '35325831003'
            ],
            [
                'id'       => 3,
                'name'     => 'Teste Seller 01',
                'email'    => 'teste_seller_01@email.com',
                'password' => Hash::make('123456'),
                'type'     => 'SELLER',
                'document' => '84124953000151'
            ],
            [
                'id'       => 4,
                'name'     => 'Teste Seller 02',
                'email'    => 'teste_seller_02@email.com',
                'password' => Hash::make('123456'),
                'type'     => 'SELLER',
                'document' => '88971548000120'
            ]
        ];

        foreach($users as $user) {
            User::factory()->create($user);
        }
    }
}
