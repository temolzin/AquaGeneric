<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PaymentsTableSeeder extends Seeder
{
    private const PAYMENT_COUNT = 1000;
    private const MIN_AMOUNT = 50; 
    private const MAX_AMOUNT = 5000;
    private const MAX_MONTHS_SUBTRACT = 12;
    private const MAX_DAYS_SUBTRACT = 28;
    private const PAYMENTS_METHODS = ['cash', 'card', 'transfer'];

    public function run()
    {
        $faker = Faker::create();
        $payments = [];
        $debtIds = DB::table('debts')->pluck('id');
        $userIds = DB::table('users')->pluck('id')->toArray();

        for ($i = 1; $i <= self::PAYMENT_COUNT; $i++) {
            $debt = DB::table('debts')->find($faker->randomElement($debtIds));

            $createdAt = $this->getRandomCreatedAt();
            $updatedAt = $createdAt;

            $payments[] = $this->createPayment($debt, $userIds, $faker, $createdAt, $updatedAt);
        }

        DB::table('payments')->insert($payments);
    }

    private function getRandomCreatedAt(): Carbon
    {
        return Carbon::now()->subMonths(rand(0, self::MAX_MONTHS_SUBTRACT))
            ->addDays(rand(0, self::MAX_DAYS_SUBTRACT));
    }

    private function createPayment($debt, array $userIds, $faker, Carbon $createdAt, Carbon $updatedAt): array
    {
        return [
            'debt_id' => $debt->id,
            'created_by' => $userIds[array_rand($userIds)],
            'amount' => rand(self::MIN_AMOUNT, self::MAX_AMOUNT),
            'locality_id' => $debt->locality_id,
            'method' => $faker->randomElement(self::PAYMENTS_METHODS),
            'note' => 'Pago correspondiente a la deuda #' . $debt->id . ' en localidad ' . $debt->locality_id,
            'deleted_at' => null,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ];
    }
}
