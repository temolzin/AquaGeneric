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
        // Pagos para Alonso son creados por AlonsoSeeder
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
