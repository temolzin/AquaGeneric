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

        // Estados posibles definidos en la migración
        $statuses = ['earring', 'in_process', 'resolved', 'closed'];

        foreach (range(1, 20) as $i) { // Genera 20 registros de prueba
            DB::table('fault_report')->insert([
                'customer_id'   => $faker->numberBetween(1, 10), // Debe coincidir con IDs reales en 'customers'
                'created_by'    => $faker->numberBetween(1, 5),  // Debe coincidir con IDs reales en 'users'
                'locality_id'   => $faker->optional()->numberBetween(1, 10), // Puede ser null
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
