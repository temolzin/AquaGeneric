<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;

class DashboardExpiringPaidDebtsSeeder extends Seeder
{
    private const MIN_AMOUNT = 100;
    private const MAX_AMOUNT = 1000;
    private const DASHBOARD_EXPIRING_DAYS = 20;

    public function run()
    {
        $faker = Faker::create();
        $today = Carbon::today();
        $limitDate = $today->copy()->addDays(self::DASHBOARD_EXPIRING_DAYS);

        $validConnections = DB::table('water_connections')
            ->whereNotNull('customer_id')
            ->inRandomOrder()
            ->take(20)
            ->get();

        $usersIds = DB::table('users')->pluck('id')->toArray();

        foreach ($validConnections as $index => $connection) {
            $amount = rand(self::MIN_AMOUNT, self::MAX_AMOUNT);
            $debtStartDate = $today->copy()->subMonths(rand(1, 3));
            $debtEndDate = $faker->dateTimeBetween($today, $limitDate);

            DB::table('debts')->insert([
                'water_connection_id' => $connection->id,
                'locality_id' => $connection->locality_id,
                'created_by' => $faker->randomElement($usersIds),
                'start_date' => $debtStartDate,
                'end_date' => $debtEndDate,
                'amount' => $amount,
                'debt_current' => 0,
                'status' => 'paid',
                'note' => 'Dashboard prueba deuda pagada próxima a vencer #' . ($index + 1),
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
