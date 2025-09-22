<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class FaultReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $localityIds = DB::table('localities')->pluck('id')->toArray();
        $userIds = DB::table('users')->pluck('id')->toArray();
        $statuses = ['Earring', 'In process', 'Resolved', 'Closed'];

        foreach (range(1, 20) as $i) {
            DB::table('fault_report')->insert([
                'customer_id'   => $faker->numberBetween(1, 10), 
                'created_by'    => $faker->randomElement($userIds),  
                'locality_id'   => $faker->randomElement($localityIds),
                'title'         => $faker->sentence(6),
                'description'   => $faker->paragraph(3),
                'status'        => $faker->randomElement($statuses),
                'date_report'   => $faker->dateTimeBetween('-6 months', 'now'),
                'created_at'    => now(),
                'updated_at'    => now(),
                'deleted_at'    => null,
            ]);
        }
    }
}

