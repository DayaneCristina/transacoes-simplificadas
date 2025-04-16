<?php

namespace Database\Seeders;

use App\Models\TransactionCode;
use Illuminate\Database\Seeder;

class TransactionCodesTableSeeder extends Seeder
{
    public function run() : void
    {
        $transactionCodes = [
            [
                'title'       => 'Transferência Recebida',
                'description' => 'Recebimento de outro usuário',
                'type'        => 'CREDIT'
            ],
            [
                'title'       => 'Transferência Realizada',
                'description' => 'Envio para outro usuário',
                'type'        => 'DEBIT'
            ],
            [
                'title'       => 'Crédito em Conta',
                'description' => 'Crédito realizado em Conta',
                'type'        => 'CREDIT'
            ]
        ];

        foreach($transactionCodes as $transactionCode) {
            TransactionCode::factory()->create($transactionCode);
        }
    }
}
