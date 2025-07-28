<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;

class AdvancePaymentsSeeder extends Seeder
{
private const COST_PER_MONTH = 100;
private const MAX_RECORDS = 20;
private const PAYMENTS_METHODS = ['cash', 'card', 'transfer'];

    public function run()
    {
        $faker = Faker::create();
        $today = Carbon::today();

        $connections = DB::table('water_connections')
            ->inRandomOrder()
            ->take(self::MAX_RECORDS)
            ->get();

        $userIds = DB::table('users')->pluck('id')->toArray();

        foreach ($connections as $index => $connection) {
            
            $monthsAdvance = 12;
            $startDate = $today->copy()->addMonths(rand(0, 3))->startOfMonth();
            $endDate = $startDate->copy()->addMonths($monthsAdvance)->subDay();
            $amount = self::COST_PER_MONTH * $monthsAdvance;

            $debtId = DB::table('debts')->insertGetId([
                'water_connection_id' => $connection->id,
                'locality_id' => $connection->locality_id,
                'created_by' => $faker->randomElement($userIds),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'amount' => $amount,
                'debt_current' => 0,
                'status' => 'paid',
                'note' => "Deuda pagada anticipadamente por $monthsAdvance meses - registro #" . ($index + 1),
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('payments')->insert([
                'customer_id' => $connection->customer_id,
                'debt_id' => $debtId,
                'created_by' => $faker->randomElement($userIds),
                'amount' => $amount,
                'locality_id' => $connection->locality_id,
                'method' => $faker->randomElement(self::PAYMENTS_METHODS),
                'note' => "Pago anticipado ($monthsAdvance meses) para deuda #$debtId",
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
