<?php

namespace Database\Seeders;

use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class DebtsTableSeeder extends Seeder
{
    private const DEBT_COUNT = 100;
    private const MIN_AMOUNT = 100;
    private const MAX_AMOUNT = 1000;
    private const DEBT_STATUSES = ['pending','partial','paid'];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $waterConnectionIds = DB::table('water_connections')->pluck('id')->toArray();
        $usersIds = DB::table('users')->pluck('id');

        $startDate = Carbon::createFromDate(2024, 1, 1);
        $endDate = Carbon::createFromDate(2024, 12, 31);

        foreach (range(1, self::DEBT_COUNT) as $index) {
            $waterConnectionId = $faker->randomElement($waterConnectionIds);
            $waterConnection = DB::table('water_connections')->find($waterConnectionId);
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
                'created_by' => $faker->randomElement($usersIds),
                'start_date' => $debtStartDate,
                'end_date' => $debtEndDate,
                'amount' => $amount,
                'debt_current' => $debtCurrent,
                'status' => $status,
                'note' => 'Deuda generada de prueba #' . $index,
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
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
