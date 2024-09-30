<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class DebtsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $now = Carbon::now();
        $customerIds = DB::table('customers')->pluck('id')->toArray();
        $localitiesIds = DB::table('localities')->pluck('id')->toArray();
        $usersIds = DB::table('users')->pluck('id')->toArray();

        foreach (range(1, 100) as $index) {
            DB::table('debts')->insert([
                'customer_id' => $faker->randomElement($customerIds),
                'locality_id' => $faker->randomElement($localitiesIds),
                'created_by' => $faker->randomElement($usersIds),
                'start_date' => now()->subMonths($faker->numberBetween(1, 12)),
                'end_date' => now()->addMonths($faker->numberBetween(1, 12)),
                'amount' => $faker->randomFloat(2, 100, 1000),
                'debt_current' => $faker->randomFloat(2, 0, 1000),
                'status' => $faker->randomElement(['pending', 'partial', 'paid']),
                'note' => 'Deuda generada de prueba #' . $index,
                'deleted_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
