<?php

namespace Database\Seeders;

use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\User;
use App\Models\DebtCategory;

class DebtsTableSeeder extends Seeder
{
    private const DEBT_COUNT = 100;
    private const MIN_AMOUNT = 100;
    private const MAX_AMOUNT = 1000;
    private const DEBT_STATUSES = ['pending', 'partial', 'paid'];

    public function run()
    {
        $faker = Faker::create();
        $startDate = Carbon::now()->subMonths(2)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        $localities = DB::table('localities')->pluck('id');
        foreach ($localities as $localityId)  {
            DebtCategory::firstOrCreate(
                [
                    'name' => 'Servicio de Agua',
                    'locality_id' => $localityId,
                ],
                [
                    'description' => 'Pago de servicio de agua',
                    'color' => 'bg-primary',
                    'created_by' => 1,
                ]
            );
        }
        $customers = DB::table('customers')
            ->where(function ($q) {
                $q->whereNotIn('user_id', [1, 5])
                  ->orWhereNull('user_id');
            })
            ->get();
        $debtCount = 0;
        foreach ($customers as $customer) {
            if ($debtCount >= self::DEBT_COUNT) break;
            $waterConnections = DB::table('water_connections')
                ->where('customer_id', $customer->id)
                ->get();
            foreach ($waterConnections as $waterConnection) {
                if ($debtCount >= self::DEBT_COUNT) break;
                $createdBy = $this->getUserForLocality($waterConnection->locality_id) ?? 1;
                $categories = DebtCategory::where('locality_id', $waterConnection->locality_id)->get();
                if ($categories->isEmpty()) continue;
                $selectedCategory = $categories->random();
                $debtStartDate = $faker->dateTimeBetween($startDate, $endDate);
                $debtDuration = $faker->numberBetween(1, 3);
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
                    'note' => 'Deuda generada #' . ($debtCount + 1),
                    'debt_category_id' => $selectedCategory->id,
                    'period_month' => Carbon::instance($debtStartDate)->month,
                    'period_year' => Carbon::instance($debtStartDate)->year,
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
            $smallvilleLocality = DB::table('localities')
                ->where('name', 'Smallville')
                ->first();
            if ($smallvilleLocality && $alonsoWaterConnections->count() >= 1) {
                $createdBy = $this->getUserForLocality($smallvilleLocality->id) ?? 1;
                $categories = DebtCategory::where('locality_id', $smallvilleLocality->id)->get();
                if (!$categories->isEmpty()) {
                    $selectedCategory = $categories->random();
                    $alonsoStartDate = Carbon::now()->subMonths(2)->startOfMonth();
                    $alonsoEndDate = Carbon::now()->subMonths(1)->endOfMonth();
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
                        'debt_category_id' => $selectedCategory->id,
                        'period_month' => $alonsoStartDate->month,
                        'period_year' => $alonsoStartDate->year,
                        'deleted_at' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
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
        if ($paymentAmount === 0) return self::DEBT_STATUSES[0];
        if ($debtCurrent > 0) return self::DEBT_STATUSES[1];
        return self::DEBT_STATUSES[2];
    }
}
