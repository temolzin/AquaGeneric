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
        $faker = Faker::create();
        $startDate = Carbon::createFromDate(2024, 1, 1);
        $endDate = Carbon::createFromDate(2024, 12, 31);

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
                    'start_date' => $debtStartDate,
                    'end_date' => $debtEndDate,
                    'amount' => $amount,
                    'debt_current' => $debtCurrent,
                    'status' => $status,
                    'note' => 'Deuda generada de prueba #' . ($debtCount + 1),
                    'deleted_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $debtCount++;
            }
        }

        $alonsoCustomerId = DB::table('customers')
            ->where('user_id', 5)
            ->value('id');

        if ($alonsoCustomerId) {
            $alonsoWaterConnections = DB::table('water_connections')
                ->where('customer_id', $alonsoCustomerId)
                ->orderBy('id', 'asc')
                ->get();

            $smallvilleLocality = DB::table('localities')->where('name', 'Smallville')->first();
            if ($smallvilleLocality) {
                $createdBy = $this->getUserForLocality($smallvilleLocality->id);

                if ($createdBy && $alonsoWaterConnections->count() >= 2) {
                    $alonsoStartDate = Carbon::now()->subMonths(2);
                    $alonsoEndDate = Carbon::now()->subMonths(1);
                    DB::table('debts')->insert([
                        'water_connection_id' => $alonsoWaterConnections[0]->id,
                        'locality_id' => $smallvilleLocality->id,
                        'created_by' => $createdBy,
                        'start_date' => $alonsoStartDate,
                        'end_date' => $alonsoEndDate,
                        'amount' => 500.00,
                        'debt_current' => 250.00,
                        'status' => 'partial',
                        'note' => 'Deuda del mes anterior',
                        'deleted_at' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    DB::table('debts')->insert([
                        'water_connection_id' => $alonsoWaterConnections[1]->id,
                        'locality_id' => $smallvilleLocality->id,
                        'created_by' => $createdBy,
                        'start_date' => $alonsoEndDate->copy()->addDay(),
                        'end_date' => Carbon::now(),
                        'amount' => 350.00,
                        'debt_current' => 175.00,
                        'status' => 'partial',
                        'note' => 'Deuda actual',
                        'deleted_at' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    private function getUserForLocality(int $localityId): ?int
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
        return !empty($userIds) ? $userIds[array_rand($userIds)] : null;
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
