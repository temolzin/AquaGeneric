<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\WaterConnection;
use Illuminate\Support\Facades\DB;

class WaterConnectionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $customers = DB::table('customers')->pluck('id');
        $localities = DB::table('localities')->pluck('id');
        $costs = DB::table('costs')->pluck('id');
        $users = DB::table('users')->pluck('id');

        foreach ($customers as $customerId) {
            WaterConnection::create([
                'customer_id' => $customerId,
                'locality_id' => $faker->randomElement($localities),
                'cost_id' => $faker->randomElement($costs),
                'created_by' => $faker->randomElement($users),
                'name' => $faker->streetName,
                'occupants_number' => $faker->numberBetween(1, 10),
                'water_days' => $faker->numberBetween(1, 7),
                'has_water_pressure' => $faker->boolean,
                'has_cistern' => $faker->boolean,
                'type' => $faker->randomElement(['residencial', 'commercial']),
            ]);

            if ($faker->boolean(30)) {
                WaterConnection::create([
                    'customer_id' => $customerId,
                    'locality_id' => $faker->randomElement($localities),
                    'cost_id' => $faker->randomElement($costs),
                    'created_by' => $faker->randomElement($users),
                    'name' => $faker->streetName,
                    'occupants_number' => $faker->numberBetween(1, 10),
                    'water_days' => $faker->numberBetween(1, 7),
                    'has_water_pressure' => $faker->boolean,
                    'has_cistern' => $faker->boolean,
                    'type' => $faker->randomElement(['residencial', 'commercial']),
                ]);
            }
        }
    }
}
