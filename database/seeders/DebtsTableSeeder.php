<?php

namespace Database\Seeders;

use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\User;

class DebtsTableSeeder extends Seeder
{
    private const DEBT_COUNT = 100;
    private const MIN_AMOUNT = 100;
    private const MAX_AMOUNT = 1000;
    private const DEBT_STATUSES = ['pending', 'partial', 'paid'];

    public function run()
    {
        $serviceId = DB::table('debt_categories')
            ->where('name', 'Servicio de Agua')
            ->value('id')
            ?? DB::table('debt_categories')->insertGetId([
                'name' => 'Servicio de Agua',
                'description' => 'Categoría global para Servicio de Agua',
                'color' => '#007bff',
                'locality_id' => null,
                'created_by' => null,
                'created_at' => now(),
            ]);

        $faker = Faker::create();
        $startDate = Carbon::now()->subMonths(2)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $customers = DB::table('customers')
            ->whereNotIn('user_id', [1, 5])
            ->orWhereNull('user_id')
            ->whereNull('deleted_at')
            ->get();

        $debtCount = 0;

        foreach ($customers as $customer) {
            if ($debtCount >= self::DEBT_COUNT) break;

            $waterConnections = DB::table('water_connections')
                ->where('customer_id', $customer->id)
                ->get();

            foreach ($waterConnections as $waterConnection) {
                if ($debtCount >= self::DEBT_COUNT) break;

                $createdBy = $this->getUserForLocality($waterConnection->locality_id);

                if (empty($createdBy)) continue;

                $debtStartDate = $faker->dateTimeBetween($startDate, $endDate);
                $debtDuration = $faker->numberBetween(1, 12);
                $debtEndDate = Carbon::instance($debtStartDate)->addMonths($debtDuration);
                $debtEndDate = min($debtEndDate, $endDate);

                $amount = rand(self::MIN_AMOUNT, self::MAX_AMOUNT);
                $paymentAmount = rand(0, $amount);
                $debtCurrent = $amount - $paymentAmount;

                $status = $this->determineDebtStatus($paymentAmount, $debtCurrent);

                DB::table('debts')->insert([
                    'water_connection_id' => $waterConnection->id,
                    'locality_id' => $waterConnection->locality_id,
                    'created_by' => $createdBy,
                    'debt_category_id' => $serviceId,
                    'start_date' => $debtStartDate,
                    'end_date' => $debtEndDate,
                    'amount' => $amount,
                    'debt_current' => $debtCurrent,
                    'status' => $status,
                    'note' => 'Deuda generada de prueba #' . ($debtCount + 1),
                    'deleted_at' => null,
                    'created_at' => now(),
                ]);

                $debtCount++;
            }
        }

        $this->seedTestDebtsByLocality($serviceId);
    }

    private function getUserForLocality(int $localityId): int
    {
        $userIds = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->whereIn('roles.name', [User::ROLE_SUPERVISOR, User::ROLE_SECRETARY])
            ->where('users.locality_id', $localityId)
            ->whereNotIn('users.id', [5])
            ->distinct()
            ->pluck('users.id')
            ->toArray();

        return $userIds ? $userIds[array_rand($userIds)] : 1;
    }

    private function determineDebtStatus(int $paymentAmount, int $debtCurrent): string
    {
        return $paymentAmount === 0
            ? self::DEBT_STATUSES[0]
            : ($debtCurrent > 0
                ? self::DEBT_STATUSES[1]
                : self::DEBT_STATUSES[2]);
    }

    private function seedTestDebtsByLocality(int $serviceId): void
    {
        if (!DB::table('discounts')->exists()) {
            $this->call(DiscountsTableSeeder::class);
        }

        $localities = DB::table('localities')
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->limit(3)
            ->get(['id']);

        foreach ($localities as $locality) {
            $connections = DB::table('water_connections')
                ->where('locality_id', $locality->id)
                ->whereNull('deleted_at')
                ->get(['id', 'customer_id']);
            $discounts = DB::table('discounts')
                ->where('locality_id', $locality->id)
                ->whereNull('deleted_at')
                ->get(['id', 'percentage']);

            if ($connections->isEmpty() || $discounts->isEmpty()) {
                continue;
            }

            $createdBy = $this->getUserForLocality($locality->id);

            for ($index = 1; $index <= 15; $index++) {
                $connection = $connections->random();
                $discount = $discounts->random();
                $originalAmount = rand(self::MIN_AMOUNT, self::MAX_AMOUNT);
                $discountAmount = round($originalAmount * ((float) $discount->percentage / 100), 2);
                $finalAmount = round($originalAmount - $discountAmount, 2);
                $startDate = Carbon::now()->subMonths(rand(0, 2));

                DB::transaction(function () use ($connection, $locality, $createdBy, $serviceId, $discount, $startDate, $finalAmount, $originalAmount, $discountAmount, $index) {
                    $debtId = DB::table('debts')->insertGetId([
                        'water_connection_id' => $connection->id,
                        'locality_id' => $locality->id,
                        'created_by' => $createdBy,
                        'debt_category_id' => $serviceId,
                        'discount_id' => $discount->id,
                        'start_date' => $startDate,
                        'end_date' => $startDate->copy()->addMonth(),
                        'amount' => $finalAmount,
                        'debt_current' => $finalAmount,
                        'status' => self::DEBT_STATUSES[0],
                        'note' => 'Deuda de prueba con descuento por localidad #' . $index,
                        'deleted_at' => null,
                        'created_at' => now(),
                    ]);

                    DB::table('discount_histories')->insert([
                        'locality_id' => $locality->id,
                        'discount_id' => $discount->id,
                        'customer_id' => $connection->customer_id,
                        'created_by' => $createdBy,
                        'module' => 'debt',
                        'record_id' => $debtId,
                        'original_amount' => $originalAmount,
                        'discount_amount' => $discountAmount,
                        'final_amount' => $finalAmount,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                });
            }
        }
    }
}
