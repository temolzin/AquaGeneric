<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GeneralExpense;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class GeneralExpensesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $localities = DB::table('localities')->pluck('id')->toArray();
        $types = ['mainteinence', 'services', 'supplies', 'taxes', 'staff'];

        foreach ($localities as $localityId) {
            $users = DB::table('users')
                ->where('locality_id', $localityId)
                ->pluck('id')
                ->toArray();

            if (empty($users)) {
                continue;
            }

            foreach (range(1, 5) as $index) {
                GeneralExpense::create([
                    'locality_id' => $localityId,
                    'created_by' => $faker->randomElement($users),
                    'concept' => $faker->word(),
                    'description' => $faker->sentence(),
                    'amount' => mt_rand(10, 50) * 100,
                    'type' => $types[array_rand($types)],
                    'expense_date' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
