<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\User;

class PaymentsTableSeeder extends Seeder
{
    private const MIN_AMOUNT = 50; 
    private const MAX_MONTHS_SUBTRACT = 12;
    private const MAX_DAYS_SUBTRACT = 28;
    private const PAYMENTS_METHODS = ['cash', 'card', 'transfer'];

    public function run()
    {
        $faker = Faker::create();
        $payments = [];
        
        $debtIds = DB::table('debts')
            ->join('water_connections', 'debts.water_connection_id', '=', 'water_connections.id')
            ->join('customers', 'water_connections.customer_id', '=', 'customers.id')
            ->pluck('debts.id');

        foreach ($debtIds as $debtId) {
            $debt = DB::table('debts')->find($debtId);

            if (!$debt) {
                continue;
            }

            $waterConnection = DB::table('water_connections')->where('id', $debt->water_connection_id)->first();

            if (!$waterConnection) {
                continue;
            }

            $localityUserIds = DB::table('users')
                ->where('locality_id', $debt->locality_id)
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
                $localityUserIds = [1];
            }

            $amount = $debt->debt_current > 0 ? $debt->debt_current : $debt->amount;

            $createdAt = $this->getRandomCreatedAt();

            $payments[] = $this->createPayment(
                $debt,
                $localityUserIds,
                $faker,
                $amount,
                $createdAt,
                $createdAt,
                $waterConnection->customer_id
            );
        }

        if (!empty($payments)) {
            foreach (array_chunk($payments, 300) as $chunk) {
                DB::table('payments')->insert($chunk);
            }
        }

        $this->seedDiscountedPayments($faker);
    }
    private function getRandomCreatedAt(): Carbon
    {
        return Carbon::now()->subMonths(rand(0, self::MAX_MONTHS_SUBTRACT))
            ->addDays(rand(0, self::MAX_DAYS_SUBTRACT));
    }

    private function createPayment($debt, array $userIds, $faker, int $amount, Carbon $createdAt, Carbon $updatedAt, int $customerId): array
    {
        $createdByUserId = null;

        if (!empty($userIds)) {
            $createdByUserId = $userIds[array_rand($userIds)];
        }

        return [
            'customer_id' => $customerId,
            'debt_id' => $debt->id,
            'created_by' => $createdByUserId,
            'amount' => $amount,
            'locality_id' => $debt->locality_id,
            'method' => $faker->randomElement(self::PAYMENTS_METHODS),
            'note' => 'Pago correspondiente a la deuda #' . $debt->id . ' en localidad ' . $debt->locality_id,
            'deleted_at' => null,
            'created_at' => $createdAt,
        ];
    }
    
     private function seedDiscountedPayments($faker): void
    {
        DB::table('localities')->whereNull('deleted_at')->orderBy('id')->each(function ($locality) use ($faker) {
            $debts = DB::table('debts')->join('water_connections', 'debts.water_connection_id', '=', 'water_connections.id')->where('debts.locality_id', $locality->id)->whereNull('debts.deleted_at')->whereNull('water_connections.deleted_at')->get(['debts.id', 'debts.amount', 'water_connections.customer_id']);

            $discounts = DB::table('discounts')->where('locality_id', $locality->id)->whereNull('deleted_at')->get(['id', 'percentage']);

            $localityUserIds = $this->getLocalityUserIds($locality->id);

            if ($debts->isEmpty() || $discounts->isEmpty() || empty($localityUserIds)) {
                $this->command->warn("La localidad {$locality->id} no cuenta con deudas, descuentos o usuarios autorizados; se omitieron sus pagos con descuento.");
                return;
            }

            $this->removeGeneratedDiscountPayments($locality->id);

            for ($index = 1; $index <= 15; $index++) {
                $debt = $debts->random();
                $discount = $discounts->random();
                $originalAmount = $faker->numberBetween(self::MIN_AMOUNT, max(self::MIN_AMOUNT, (int) $debt->amount));
                $discountAmount = round($originalAmount * ($discount->percentage / 100), 2);
                $finalAmount = round($originalAmount - $discountAmount, 2);
                $createdAt = Carbon::now();
                $createdBy = $localityUserIds[array_rand($localityUserIds)];

                DB::transaction(function () use ($locality, $debt, $discount, $createdBy, $originalAmount, $discountAmount, $finalAmount, $createdAt, $faker, $index) {
                    $paymentId = DB::table('payments')->insertGetId([
                        'customer_id' => $debt->customer_id,
                        'debt_id' => $debt->id,
                        'created_by' => $createdBy,
                        'locality_id' => $locality->id,
                        'discount_id' => $discount->id,
                        'amount' => $finalAmount,
                        'method' => $faker->randomElement(self::PAYMENTS_METHODS),
                        'note' => '[discount-seeder] Pago con descuento #' . $index,
                        'deleted_at' => null,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);

                    DB::table('discount_histories')->insert([
                        'locality_id' => $locality->id,
                        'discount_id' => $discount->id,
                        'customer_id' => $debt->customer_id,
                        'created_by' => $createdBy,
                        'module' => 'payment',
                        'record_id' => $paymentId,
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

    private function getLocalityUserIds(int $localityId): array
    {
        return DB::table('users')->where('locality_id', $localityId)->whereNull('deleted_at')->whereIn('id', DB::table('model_has_roles')->whereIn('role_id', DB::table('roles')->whereIn('name', [User::ROLE_SUPERVISOR, User::ROLE_SECRETARY])->pluck('id'))->pluck('model_id'))->pluck('id')->toArray();
    }

    private function removeGeneratedDiscountPayments(int $localityId): void
    {
        $paymentIds = DB::table('payments')->where('locality_id', $localityId)->where('note', 'like', '[discount-seeder] Pago con descuento%')->pluck('id');

        if ($paymentIds->isEmpty()) {
            return;
        }

        DB::table('discount_histories')->where('module', 'payment')->whereIn('record_id', $paymentIds)->delete();

        DB::table('payments')->whereIn('id', $paymentIds)->delete();
    }
}
