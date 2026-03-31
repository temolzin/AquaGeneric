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
    private const DEBT_STATUSES = ['pending','partial','paid'];

    public function run()
    {
        // Ensure service category exists for compatibility
        $service = DB::table('debt_categories')->where('name', 'Servicio de Agua')->first();
        if (! $service) {
            $serviceId = DB::table('debt_categories')->insertGetId([
                'name' => 'Servicio de Agua',
                'description' => 'Categoría global para Servicio de Agua',
                'color' => '#007bff',
                'locality_id' => null,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $serviceId = $service->id;
        }

        $faker = Faker::create();
        $startDate = Carbon::now()->subMonths(2)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $alonso = DB::table('customers')->where('user_id', 5)->first();
        if ($alonso) {
            $waterConnections = DB::table('water_connections')
                ->where('customer_id', $alonso->id)
                ->limit(2)
                ->get();

            foreach ($waterConnections as $waterConnection) {
                $createdBy = $this->getUserForLocality($waterConnection->locality_id);
                if (!$createdBy) {
                    continue;
                }

                $debtStartDate = $faker->dateTimeBetween($startDate, $endDate);
                $debtDuration = $faker->numberBetween(1, 12);
                $debtEndDate = Carbon::instance($debtStartDate)->addMonths($debtDuration);

                if ($debtEndDate > $endDate) {
                    $debtEndDate = $endDate;
                }

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
                    'note' => 'Deuda generada de prueba para Alonso',
                    'deleted_at' => null,
                    'created_at' => now(),
                ]);
            }
        }

        $customers = DB::table('customers')
            ->whereNotIn('user_id', [1, 5])
            ->orWhereNull('user_id')
            ->get();

        $debtCount = 0;
        foreach ($customers as $customer) {
            if ($debtCount >= self::DEBT_COUNT) {
                break;
            }

            $waterConnections = DB::table('water_connections')
                ->where('customer_id', $customer->id)
                ->get();

            foreach ($waterConnections as $waterConnection) {
                if ($debtCount >= self::DEBT_COUNT) {
                    break;
                }

                $createdBy = $this->getUserForLocality($waterConnection->locality_id);
                if (!$createdBy) {
                    continue;
                }

                $debtStartDate = $faker->dateTimeBetween($startDate, $endDate);
                $debtDuration = $faker->numberBetween(1, 12);
                $debtEndDate = Carbon::instance($debtStartDate)->addMonths($debtDuration);

                if ($debtEndDate > $endDate) {
                    $debtEndDate = $endDate;
                }

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
    }

    private function getUserForLocality(int $localityId): int
    {
        $userIds = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->whereIn('roles.name', [User::ROLE_SUPERVISOR, User::ROLE_SECRETARY])
            ->where('users.locality_id', $localityId)
            ->whereNotIn('users.id', [1, 5])
            ->distinct()
            ->pluck('users.id')
            ->toArray();

        return !empty($userIds) ? $userIds[array_rand($userIds)] : 1;
    }

    private function determineDebtStatus(int $paymentAmount, int $debtCurrent): string
    {
        if ($paymentAmount === 0) {
            return self::DEBT_STATUSES[0];
        } elseif ($debtCurrent > 0) {
            return self::DEBT_STATUSES[1];
        } else {
            return self::DEBT_STATUSES[2];
        }
    }
}
