<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\Customer;

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
        $userIds = DB::table('users')->pluck('id')->toArray();

        foreach (range(1, 150) as $index) {
            DB::table('customers')->insert([
                'name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'block' => $faker->word,
                'street' => $faker->streetName,
                'interior_number' => $faker->numberBetween(1, 100),
                'marital_status' => $faker->boolean,
                'status' => $faker->boolean, 
                'locality_id' => $faker->randomElement($localityIds),
                'created_by' => $faker->randomElement($userIds),
            ]);
        }
    }
}
