<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class LogWaterConnectionTransferSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $waterConnection = DB::table('water_connections')->first();
        $oldCustomer = DB::table('customers')->where('status', 0)->first();
        $newCustomer = DB::table('customers')->where('status', 1)->first();
        $user = DB::table('users')->first();

        if (!$waterConnection || !$oldCustomer || !$newCustomer || !$user) {
            return;
        }

        DB::table('log_water_connection_transfer')->insert([
            'water_connection_id' => $waterConnection->id,
            'old_customer_id' => $oldCustomer->id,
            'new_customer_id' => $newCustomer->id,
            'reason' => 'death',
            'effective_date' => date('Y-m-d'),
            'note' => 'Transferencia de prueba (fallecimiento) generada por seeder.',
            'created_by' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
