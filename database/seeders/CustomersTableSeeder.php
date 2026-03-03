<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\Customer;
use App\Models\User;
use App\Models\Locality;

class CustomersTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $localityIds = DB::table('localities')->pluck('id')->toArray();

        foreach (range(1, 150) as $index) {
            $locality_id = $faker->randomElement($localityIds);

            $userIds = DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->whereIn('roles.name', [User::ROLE_SUPERVISOR, User::ROLE_SECRETARY])
                ->where('users.locality_id', $locality_id)
                ->distinct()
                ->pluck('users.id')
                ->toArray();

            $status = $faker->boolean;
            $responsibleName = $status ? null : $faker->name;

            DB::table('customers')->insert([
                'name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => $faker->unique()->safeEmail,
                'locality' => $faker->city,
                'state' => $faker->state,
                'zip_code' => $faker->regexify('[0-9]{5}'),
                'block' => $faker->word,
                'street' => $faker->streetName,
                'exterior_number' => $faker->numberBetween(1, 100),
                'interior_number' => $faker->numberBetween(1, 100),
                'marital_status' => $faker->boolean,
                'status' => $status,
                'responsible_name' => $responsibleName,
                'locality_id' => $locality_id,
                'created_by' => $faker->randomElement($userIds),
            ]);
        }

        // Agregar cliente Alonso específico
        $alonso = User::find(5);
        if ($alonso) {
            $locality = Locality::where('name', 'Smallville')->first();
            if ($locality) {
                Customer::updateOrCreate(
                    ['user_id' => $alonso->id],
                    [
                        'name' => 'Alonso',
                        'last_name' => 'Gutiérrez López',
                        'email' => $alonso->email,
                        'locality' => 'Smallville',
                        'state' => 'Kansas',
                        'zip_code' => '66002',
                        'block' => '1',
                        'street' => 'Calle Principal',
                        'exterior_number' => '123',
                        'interior_number' => '',
                        'marital_status' => 0,
                        'status' => 1,
                        'responsible_name' => null,
                        'locality_id' => $locality->id,
                        'created_by' => 1,
                    ]
                );
            }
        }
    }
}
