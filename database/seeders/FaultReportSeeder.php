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

        $statuses = ['earring', 'in_process', 'resolved', 'closed'];

        foreach (range(1, 20) as $i) {
            DB::table('fault_report')->insert([
                'customer_id'   => $faker->numberBetween(1, 10), 
                'created_by'    => $faker->numberBetween(1, 5),  
                'locality_id'   => $faker->optional()->numberBetween(1, 10), 
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

