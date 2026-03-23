<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

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
        $debtIds = DB::table('debts')->pluck('id');

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

            $customer = DB::table('customers')->where('id', $waterConnection->customer_id)->first();
            if ($customer && $customer->user_id) {
                $localityUserIds = array_diff($localityUserIds, [$customer->user_id]);
                $localityUserIds = array_values($localityUserIds);
            }

            if (empty($localityUserIds)) {
                continue;
            }

            $this->handlePayment($debt, $localityUserIds, $faker, $waterConnection, $payments);
        }

        DB::table('payments')->insert($payments);
    }

    private function handlePayment($debt, array $userIds, $faker, $waterConnection, &$payments)
    {
        $remainingDebt = $debt->debt_current;

        while ($remainingDebt > 0) {
            $amount = ($remainingDebt < self::MIN_AMOUNT)
                ? $remainingDebt
                : rand(self::MIN_AMOUNT, min($remainingDebt, $debt->debt_current));

            $remainingDebt -= $amount;

            $createdAt = $this->getRandomCreatedAt();

            $payments[] = $this->createPayment(
                $debt,
                $userIds,
                $faker,
                $amount,
                $createdAt,
                $createdAt,
                $waterConnection->customer_id
            );
        }
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
}
