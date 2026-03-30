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

        $alonsoCustomerId = DB::table('customers')
            ->where('user_id', 5)
            ->value('id') ?? 0;

        $customerIdsWithEmail = DB::table('customers')
            ->whereNotNull('email')
            ->where('id', '!=', $alonsoCustomerId)
            ->pluck('id');

        $validConnections = DB::table('water_connections')
            ->whereIn('customer_id', $customerIdsWithEmail)
            ->inRandomOrder()
            ->take(20)
            ->get();

        foreach ($validConnections as $index => $connection) {
            $usersIds = DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->whereIn('roles.name', ['Supervisor', 'Secretaria'])
                ->where('users.locality_id', $connection->locality_id)
                ->where('users.id', '!=', 5)
                ->distinct()
                ->pluck('users.id')
                ->toArray();
            
            if (empty($usersIds)) {
                $usersIds = DB::table('users')
                    ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->whereIn('roles.name', ['Supervisor', 'Secretaria'])
                    ->where('users.id', '!=', 5)
                    ->distinct()
                    ->pluck('users.id')
                    ->toArray();
            }
            
            if (empty($usersIds)) {
                continue;
            }
            
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
            ]);
        }
    }
}
