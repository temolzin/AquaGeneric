<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentsTableSeeder extends Seeder
{
    public function run()
    {
        $payments = [];
        $now = now();

        for ($i = 1; $i <= 1000; $i++) {
            $payments[] = [
                'debt_id' => $i,
                'amount' => 100, 
                'payment_date' => $now,
                'note' => 'Pago correspondiente a la deuda #' . $i,
                'deleted_at' => null
            ];
        }
        DB::table('payments')->insert($payments);
    }
}
