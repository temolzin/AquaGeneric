<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\WaterConnection;
use App\Models\User;
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
            $customers = DB::table('customers')
                ->where('locality_id', $localityId)
                ->whereNull('deleted_at')
                ->whereNotIn('id', [DB::table('customers')->where('user_id', 5)->value('id') ?? 0])
                ->pluck('id')
                ->toArray();
            
            $costs = DB::table('costs')->where('locality_id', $localityId)->pluck('id')->toArray();
            
            if (empty($costs)) {
                $allCosts = DB::table('costs')->pluck('id')->toArray();
                $costs = !empty($allCosts) ? $allCosts : [];
            }
            
            $users = DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->whereIn('roles.name', [User::ROLE_SUPERVISOR, User::ROLE_SECRETARY])
                ->where('users.locality_id', $localityId)
                ->distinct()
                ->pluck('users.id')
                ->toArray();
            $localitySections = DB::table('sections')->where('locality_id', $localityId)->pluck('id')->toArray();

            if (empty($localitySections) || empty($users) || empty($costs)) {
                continue;
            }

            DB::table('water_connections')->where('locality_id', $localityId)->whereNull('section_id')
                ->update([
                    'section_id' => $faker->randomElement($localitySections)
            ]);

            foreach ($customers as $customerId) {

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
                    'section_id' => $faker->randomElement($localitySections), 
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
                        'section_id' => $faker->randomElement($localitySections),
                    ]);
                }
            }
        }

        $alonsoCustomer = DB::table('customers')->where('user_id', 5)->first();
        $smallvilleLocality = DB::table('localities')->where('name', 'Smallville')->first();
        
        if ($alonsoCustomer && $smallvilleLocality) {
            
            $alonsoConnectionCount = DB::table('water_connections')
                ->where('customer_id', $alonsoCustomer->id)
                ->count();
            
            if ($alonsoConnectionCount === 0) {
                $costs = DB::table('costs')->where('locality_id', $smallvilleLocality->id)->pluck('id')->toArray();
                
                if (empty($costs)) {
                    $allCosts = DB::table('costs')->pluck('id')->toArray();
                    $costs = !empty($allCosts) ? $allCosts : [];
                }
                
                $users = DB::table('users')
                    ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->whereIn('roles.name', [User::ROLE_SUPERVISOR, User::ROLE_SECRETARY])
                    ->where('users.locality_id', $smallvilleLocality->id)
                    ->distinct()
                    ->pluck('users.id')
                    ->toArray();
                $sections = DB::table('sections')->where('locality_id', $smallvilleLocality->id)->pluck('id')->toArray();
                
                if (!empty($costs) && !empty($users) && !empty($sections)) {
                    WaterConnection::create([
                        'customer_id' => $alonsoCustomer->id,
                        'locality_id' => $smallvilleLocality->id,
                        'cost_id' => $costs[0],
                        'created_by' => $users[0],
                        'name' => 'Cierra Hermosa',
                        'block' => 'Tecamac',
                        'street' => 'Calle Principal',
                        'exterior_number' => '123',
                        'interior_number' => 'A',
                        'occupants_number' => 4,
                        'water_days' => json_encode(['monday', 'wednesday']),
                        'has_water_pressure' => true,
                        'has_cistern' => false,
                        'type' => 'residencial',
                        'section_id' => $sections[0],
                    ]);

                    WaterConnection::create([
                        'customer_id' => $alonsoCustomer->id,
                        'locality_id' => $smallvilleLocality->id,
                        'cost_id' => $costs[0],
                        'created_by' => $users[0],
                        'name' => 'Casas Javer',
                        'block' => 'Tecamac',
                        'street' => 'Calle Principal',
                        'exterior_number' => '147',
                        'interior_number' => '89',
                        'occupants_number' => 2,
                        'water_days' => json_encode(['tuesday', 'thursday']),
                        'has_water_pressure' => true,
                        'has_cistern' => true,
                        'type' => 'residencial',
                        'section_id' => $sections[0],
                    ]);
                }
            }
        }
    }
}
