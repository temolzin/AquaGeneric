<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;

class AdvancePaymentsSeeder extends Seeder {
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

        foreach ($connections as $index => $connection) {
            $monthsAdvance = 12;
            $createdAt = $today->copy()->subMonths(rand(0, 12))->startOfMonth()->addDays(rand(0, 28));
            $startDate = $today->copy()->addMonths(rand(0, 3))->startOfMonth();
            $endDate = $startDate->copy()->addMonths($monthsAdvance)->subDay();
            $amount = self::COST_PER_MONTH * $monthsAdvance;

            $localityUserIds = DB::table('users')
                ->where('locality_id', $connection->locality_id)
                ->where('id', '!=', 5)
                ->whereIn('id', DB::table('model_has_roles')
                    ->whereIn('role_id', DB::table('roles')
                        ->whereIn('name', ['Supervisor', 'Secretaria'])
                        ->pluck('id')
                    )
                    ->pluck('model_id')
                )
                ->pluck('id')
                ->toArray();

            if (empty($localityUserIds)) {
                $localityUserIds = DB::table('users')
                    ->where('id', '!=', 5)
                    ->whereIn('id', DB::table('model_has_roles')
                        ->whereIn('role_id', DB::table('roles')
                            ->whereIn('name', ['Supervisor', 'Secretaria'])
                            ->pluck('id')
                        )
                        ->pluck('model_id')
                    )
                    ->pluck('id')
                    ->toArray();
            }

            if (empty($localityUserIds)) {
                continue;
            }

            $userId = $faker->randomElement($localityUserIds);

            $debtId = DB::table('debts')->insertGetId([
                'water_connection_id' => $connection->id,
                'locality_id' => $connection->locality_id,
                'created_by' => $userId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'amount' => $amount,
                'debt_current' => 0,
                'status' => 'paid',
                'note' => "Deuda pagada anticipadamente por $monthsAdvance meses - registro #" . ($index + 1),
                'deleted_at' => null,
                'created_at' => $createdAt,
            ]);

            DB::table('payments')->insert([
                'customer_id' => $connection->customer_id,
                'debt_id' => $debtId,
                'created_by' => $userId,
                'amount' => $amount,
                'locality_id' => $connection->locality_id,
                'method' => $faker->randomElement(self::PAYMENTS_METHODS),
                'note' => "Pago anticipado ($monthsAdvance meses) para deuda #$debtId",
                'is_future_payment' => 1,
                'deleted_at' => null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }
}
