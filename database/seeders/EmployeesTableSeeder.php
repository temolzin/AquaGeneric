<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

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
        $userIds = DB::table('users')->pluck('id')->toArray();
        
        $roles = ['Administrador', 'Recepcionista','Encargado','Seguridad'];

        foreach (range(1, 20) as $index) {
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
                'locality_id' => $faker->randomElement($localityIds),
                'created_by' => $faker->randomElement($userIds),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
