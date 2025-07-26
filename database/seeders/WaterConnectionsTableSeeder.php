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

        $localities = DB::table('localities')->pluck('id')->toArray();

        foreach ($localities as $localityId) {
            $customers = DB::table('customers')->where('locality_id', $localityId)->whereNull('deleted_at')->pluck('id')->toArray();
            $costs = DB::table('costs')->where('locality_id', $localityId)->pluck('id')->toArray();
            $users = DB::table('users')->pluck('id')->toArray();

            foreach ($customers as $customerId) {

                if (empty($costs)) {
                    continue;
                }

                WaterConnection::create([
                    'customer_id' => $customerId,
                    'locality_id' => $localityId,
                    'street' => $faker->streetName,
                    'block' => $faker->word,
                    'exterior_number' => $faker->numberBetween(1, 100),
                    'interior_number' => $faker->numberBetween(1, 100),
                    'cost_id' => $faker->randomElement($costs),
                    'created_by' => $faker->randomElement($users),
                    'name' => $faker->streetName,
                    'occupants_number' => $faker->numberBetween(1, 10),
                    'water_days' => $faker->boolean(20) ? json_encode('all') : json_encode($faker->randomElements([
                        'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'
                    ])),
                    'has_water_pressure' => $faker->boolean,
                    'has_cistern' => $faker->boolean,
                    'type' => $faker->randomElement(['residencial', 'commercial']),
                ]);

                if ($faker->boolean(30)) {
                    WaterConnection::create([
                        'customer_id' => $customerId,
                        'locality_id' => $localityId,
                        'street' => $faker->streetName,
                        'block' => $faker->word,
                        'exterior_number' => $faker->numberBetween(1, 100),
                        'interior_number' => $faker->numberBetween(1, 100),
                        'cost_id' => $faker->randomElement($costs),
                        'created_by' => $faker->randomElement($users),
                        'name' => $faker->streetName,
                        'occupants_number' => $faker->numberBetween(1, 10),
                        'water_days' => $faker->boolean(20) ? json_encode('all') : json_encode($faker->randomElements([
                            'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'
                        ])),
                        'has_water_pressure' => $faker->boolean,
                        'has_cistern' => $faker->boolean,
                        'type' => $faker->randomElement(['residencial', 'commercial']),
                    ]);
                }
            }
        }
    }
}
