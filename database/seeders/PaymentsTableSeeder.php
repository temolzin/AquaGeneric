<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class PaymentsTableSeeder extends Seeder
{
    private const PAYMENT_COUNT = 1000;
    private const MIN_AMOUNT = 50; 
    private const MAX_AMOUNT = 500;
    private const MAX_MONTHS_SUBTRACT = 12;
    private const MAX_DAYS_SUBTRACT = 28;

    public function run()
    {
        $faker = Faker::create();
        $payments = [];
        $localityIds = DB::table('localities')->pluck('id')->toArray();
        $userIds = DB::table('users')->pluck('id')->toArray();

        for ($i = 1; $i <= self::PAYMENT_COUNT; $i++) {
            $randomLocalityId = $localityIds[array_rand($localityIds)];
            $debtIds = DB::table('debts')->pluck('id')->toArray();

            $createdAt = Carbon::now()->subMonths(rand(0, self::MAX_MONTHS_SUBTRACT))->addDays(rand(0, self::MAX_DAYS_SUBTRACT));
            $updatedAt = $createdAt; 

            $payments[] = [
                'debt_id' => $faker->randomElement($debtIds),
                'created_by' => $userIds[array_rand($userIds)],
                'amount' => rand(self::MIN_AMOUNT, self::MAX_AMOUNT),
                'locality_id' => $randomLocalityId,
                'note' => 'Pago correspondiente a la deuda #' . $i . ' en localidad ' . $randomLocalityId,
                'deleted_at' => null,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ];
        }
        DB::table('payments')->insert($payments);
    }
}
