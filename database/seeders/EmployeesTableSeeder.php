<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\User;

class EmployeesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $localityIds = DB::table('localities')->pluck('id')->toArray();
        
        foreach (range(1, 20) as $index) {
            $locality_id = $faker->randomElement($localityIds);

            $userIds = DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->whereIn('roles.name', [User::ROLE_SUPERVISOR, User::ROLE_SECRETARY])
                ->where('users.locality_id', $locality_id)
                ->distinct()
                ->pluck('users.id')
                ->toArray();

            $roles = ['Administrador', 'Recepcionista','Encargado','Seguridad'];
        
            DB::table('employees')->insert([
                'name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => $faker->unique()->safeEmail,
                'phone_number' => $faker->phoneNumber,
                'salary' => $faker->numberBetween(10000, 50000),
                'rol' => $faker->randomElement($roles),
                'locality' => $faker->city,
                'state' => $faker->state,
                'zip_code' => $faker->regexify('[0-9]{5}'),
                'block' => $faker->word,
                'street' => $faker->streetName,
                'exterior_number' => $faker->numberBetween(1, 100),
                'interior_number' => $faker->numberBetween(1, 100),
                'locality_id' => $locality_id,
                'created_by' => $faker->randomElement($userIds),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
