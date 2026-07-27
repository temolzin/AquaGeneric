<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DebtsTableSeeder extends Seeder
{
    private const DEBT_COUNT = 100;
    private const MIN_AMOUNT = 100;
    private const MAX_AMOUNT = 1000;
    private const DEBT_STATUSES = ['pending', 'partial', 'paid'];
    private const DISCOUNTED_DEBTS_PER_LOCALITY = 15;
    private const DISCOUNT_SEED_NOTE = '[discount-seeder] Deuda con descuento';

    public function run(): void
    {
        $serviceId = $this->getServiceCategoryId();
        $this->seedRegularDebts($serviceId);
        $this->seedDiscountedDebts($serviceId);
    }

    private function seedRegularDebts(int $serviceId): void
    {
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
            if ($debtCount >= self::DEBT_COUNT) {
                break;
            }

            $connections = DB::table('water_connections')->where('customer_id', $customer->id)->get();
            foreach ($connections as $connection) {
                if ($debtCount >= self::DEBT_COUNT) {
                    break;
                }

                $createdBy = $this->getUserForLocality($connection->locality_id);
                if (!$createdBy) {
                    continue;
                }

                $debtStartDate = $faker->dateTimeBetween($startDate, $endDate);
                $debtDuration = $faker->numberBetween(1, 12);
                $debtEndDate = Carbon::instance($debtStartDate)->addMonths($debtDuration);
                $debtEndDate = min($debtEndDate, $endDate);
                $amount = random_int(self::MIN_AMOUNT, self::MAX_AMOUNT);
                $paymentAmount = random_int(0, $amount);
                $debtCurrent = $amount - $paymentAmount;

                DB::table('debts')->insert([
                    'water_connection_id' => $connection->id,
                    'locality_id' => $connection->locality_id,
                    'created_by' => $createdBy,
                    'debt_category_id' => $serviceId,
                    'start_date' => $debtStartDate,
                    'end_date' => $debtEndDate,
                    'amount' => $amount,
                    'debt_current' => $debtCurrent,
                    'status' => $this->determineDebtStatus($paymentAmount, $debtCurrent),
                    'note' => 'Deuda generada de prueba #' . ($debtCount + 1),
                    'deleted_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $debtCount++;
            }
        }
    }

    private function seedDiscountedDebts(int $serviceId): void
    {
        DB::table('localities')->whereNull('deleted_at')->orderBy('id')->each(function ($locality) use ($serviceId) {
            $connections = DB::table('water_connections')->where('locality_id', $locality->id)->whereNull('deleted_at')->get(['id', 'customer_id']);
            $discounts = DB::table('discounts')->where('locality_id', $locality->id)->whereNull('deleted_at')->get(['id', 'percentage']);
            $createdBy = $this->getUserForLocality($locality->id, false);

            if ($connections->isEmpty() || $discounts->isEmpty() || !$createdBy) {
                $this->command->warn("La localidad {$locality->id} no cuenta con conexiones, descuentos o un usuario autorizado; se omitieron sus deudas con descuento.");
                return;
            }

            $this->removeGeneratedDiscountDebts($locality->id);

            for ($index = 1; $index <= self::DISCOUNTED_DEBTS_PER_LOCALITY; $index++) {
                $connection = $connections->random();
                $discount = $discounts->random();
                $originalAmount = random_int(self::MIN_AMOUNT, self::MAX_AMOUNT);
                $discountAmount = round($originalAmount * ((float) $discount->percentage / 100), 2);
                $finalAmount = round($originalAmount - $discountAmount, 2);
                $createdAt = Carbon::now();

                DB::transaction(function () use ($locality, $connection, $discount, $createdBy, $serviceId, $originalAmount, $discountAmount, $finalAmount, $createdAt, $index) {
                    $debtId = DB::table('debts')->insertGetId([
                        'water_connection_id' => $connection->id,
                        'locality_id' => $locality->id,
                        'created_by' => $createdBy,
                        'debt_category_id' => $serviceId,
                        'discount_id' => $discount->id,
                        'start_date' => $createdAt->toDateString(),
                        'end_date' => $createdAt->copy()->endOfMonth()->toDateString(),
                        'amount' => $finalAmount,
                        'debt_current' => $finalAmount,
                        'status' => 'pending',
                        'note' => self::DISCOUNT_SEED_NOTE . " #{$index}",
                        'deleted_at' => null,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
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
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);
                });
            }
        });
    }

    private function getServiceCategoryId(): int
    {
        return DB::table('debt_categories')->where('name', 'Servicio de Agua')->value('id')
            ?? DB::table('debt_categories')->insertGetId([
                'name' => 'Servicio de Agua',
                'description' => 'Categoría global para Servicio de Agua',
                'color' => '#007bff',
                'locality_id' => null,
                'created_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    }

    private function getUserForLocality(int $localityId, bool $allowOriginalFallback = true): ?int
    {
        $userIds = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->whereIn('roles.name', [User::ROLE_SUPERVISOR, User::ROLE_SECRETARY])
            ->where('users.locality_id', $localityId)
            ->whereNull('users.deleted_at')
            ->whereNotIn('users.id', [5])
            ->distinct()
            ->pluck('users.id')
            ->all();

        if (!empty($userIds)) {
            return $userIds[array_rand($userIds)];
        }

        return $allowOriginalFallback && DB::table('users')->where('id', 1)->exists() ? 1 : null;
    }

    private function determineDebtStatus(int $paymentAmount, int $debtCurrent): string
    {
        return $paymentAmount === 0 ? self::DEBT_STATUSES[0] : ($debtCurrent > 0 ? self::DEBT_STATUSES[1] : self::DEBT_STATUSES[2]);
    }

    private function removeGeneratedDiscountDebts(int $localityId): void
    {
        $debtIds = DB::table('debts')->where('locality_id', $localityId)->where('note', 'like', self::DISCOUNT_SEED_NOTE . '%')->pluck('id');
        if ($debtIds->isEmpty()) {
            return;
        }

        $paymentIds = DB::table('payments')->whereIn('debt_id', $debtIds)->pluck('id');
        DB::table('discount_histories')->where('module', 'debt')->whereIn('record_id', $debtIds)->delete();
        if ($paymentIds->isNotEmpty()) {
            DB::table('discount_histories')->where('module', 'payment')->whereIn('record_id', $paymentIds)->delete();
        }
        DB::table('debts')->whereIn('id', $debtIds)->delete();
    }
}
