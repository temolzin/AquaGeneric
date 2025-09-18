<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class InventoryTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $localityIds = DB::table('localities')->pluck('id')->toArray();
        $userIds = DB::table('users')->pluck('id')->toArray();

        if (empty($localityIds) || empty($userIds)) {
            throw new \Exception('No hay IDs disponibles en las tablas localities o users.');
        }

        $inventoryData = [
            [
                'locality_id' => $faker->randomElement($localityIds),
                'created_by' => $faker->randomElement($userIds),
                'name' => 'Válvula de bola 1 pulgada',
                'description' => 'Válvula para control de flujo en sistemas de agua potable',
                'amount' => 75,
                'category' => 'válvulas',
                'material' => 'PVC',
                'dimensions' => '1 pulgada',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'locality_id' => $faker->randomElement($localityIds),
                'created_by' => $faker->randomElement($userIds),
                'name' => 'Tubería de acero 2 pulgadas',
                'description' => 'Tubería para distribución de agua en alta presión',
                'amount' => 150,
                'category' => 'tuberías',
                'material' => 'acero inoxidable',
                'dimensions' => '2 pulgadas',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'locality_id' => $faker->randomElement($localityIds),
                'created_by' => $faker->randomElement($userIds),
                'name' => 'Medidor de flujo 50 mm',
                'description' => 'Medidor digital para monitoreo de consumo de agua',
                'amount' => 20,
                'category' => 'medidores',
                'material' => 'latón',
                'dimensions' => '50 mm',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'locality_id' => $faker->randomElement($localityIds),
                'created_by' => $faker->randomElement($userIds),
                'name' => 'Filtro de agua 3/4 pulgada',
                'description' => 'Filtro para purificación en sistemas de riego',
                'amount' => 30,
                'category' => 'filtros',
                'material' => 'plástico',
                'dimensions' => '3/4 pulgada',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'locality_id' => $faker->randomElement($localityIds),
                'created_by' => $faker->randomElement($userIds),
                'name' => 'Conector de codo 25 mm',
                'description' => 'Conector para unión de tuberías en ángulo de 90 grados',
                'amount' => 100,
                'category' => 'conectores',
                'material' => 'PVC',
                'dimensions' => '25 mm',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        foreach ($inventoryData as $data) {
            DB::table('inventory')->insert($data);
        }
    }
}