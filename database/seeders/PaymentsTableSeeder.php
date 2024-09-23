<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class PaymentsTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $payments = [];
        $now = Carbon::now();
        $localityIds = DB::table('localities')->pluck('id')->toArray();
        $userIds = DB::table('users')->pluck('id')->toArray();

        for ($i = 1; $i <= 1000; $i++) {
            $randomLocalityId = $localityIds[array_rand($localityIds)];
            $randomMonth = rand(1, 12);
            $randomDay = rand(1, 28);
            $paymentDate = Carbon::createFromDate($now->year, $randomMonth, $randomDay);
            $debtIds = DB::table('debts')->pluck('id')->toArray();

            $payments[] = [
                'debt_id' => $faker->randomElement($debtIds),
                'created_by' => $userIds[array_rand($userIds)],
                'amount' => rand(50, 500),
                'payment_date' => $paymentDate,
                'locality_id' => $randomLocalityId,
                'note' => 'Pago correspondiente a la deuda #' . $i . ' en localidad ' . $randomLocalityId,
                'deleted_at' => null
            ];
        }
        DB::table('payments')->insert($payments);
    }
}
