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
    private const MIN_DEBT_CURRENT = 0;
    private const MAX_DEBT_CURRENT = 1000;
    private const STATUS_OPTIONS = ['pending', 'partial', 'paid'];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $customerIds = DB::table('customers')->pluck('id')->toArray();
        $localitiesIds = DB::table('localities')->pluck('id')->toArray();
        $usersIds = DB::table('users')->pluck('id')->toArray();
        $createdAt = $faker->dateTimeBetween('-1 year', 'now');
        $updatedAt = $createdAt;

        $startDate = Carbon::now()->subYear();

        foreach (range(1, self::DEBT_COUNT) as $index) {
            $createdAt = $faker->dateTimeBetween('-1 year', 'now');
            $startDate = Carbon::instance($createdAt)->subMonths($faker->numberBetween(1, 12));
            $endDate = Carbon::instance($createdAt)->addMonths($faker->numberBetween(1, 12));
            $updatedAt = $createdAt; 

            DB::table('debts')->insert([
                'customer_id' => $faker->randomElement($customerIds),
                'locality_id' => $faker->randomElement($localitiesIds),
                'created_by' => $faker->randomElement($usersIds),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'amount' => $faker->randomFloat(2, self::MIN_AMOUNT, self::MAX_AMOUNT),
                'debt_current' => $faker->randomFloat(2, self::MIN_DEBT_CURRENT, self::MAX_DEBT_CURRENT),
                'status' => $faker->randomElement(self::STATUS_OPTIONS),
                'note' => 'Deuda generada de prueba #' . $index,
                'deleted_at' => null,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ]);
        }
    }
}
