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

        DB::table('inventory')->delete();

        DB::statement('ALTER TABLE inventory AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE log_inventory AUTO_INCREMENT = 1');

        $categories = DB::table('inventory_categories')
            ->select('id', 'name', 'locality_id')
            ->get();

        $categoryMap = [];
        foreach ($categories as $category) {
            $categoryMap[$category->locality_id][$category->name] = $category->id;
        }

        $inventoryData = [];

        foreach ($localityIds as $localityId) {
            $localCategories = $categoryMap[$localityId] ?? [];

            if (empty($localCategories)) {
                $this->command->warn("No hay categorías para la localidad ID: {$localityId}");
                continue;
            }

            $items = [
                [
                    'name' => 'Válvula de bola 1 pulgada',
                    'description' => 'Válvula para control de flujo en sistemas de agua potable',
                    'amount' => 75,
                    'target_category' => 'Válvulas y Reguladores',
                    'material' => 'PVC',
                    'dimensions' => '1 pulgada',
                ],
                [
                    'name' => 'Tubería de acero 2 pulgadas',
                    'description' => 'Tubería para distribución de agua en alta presión',
                    'amount' => 150,
                    'target_category' => 'Tuberías y Conexiones',
                    'material' => 'acero inoxidable',
                    'dimensions' => '2 pulgadas',
                ],
                [
                    'name' => 'Medidor de flujo 50 mm',
                    'description' => 'Medidor digital para monitoreo de consumo de agua',
                    'amount' => 20,
                    'target_category' => 'Medidores de Agua',
                    'material' => 'latón',
                    'dimensions' => '50 mm',
                ],
                [
                    'name' => 'Filtro de agua 3/4 pulgada',
                    'description' => 'Filtro para purificación en sistemas de riego',
                    'amount' => 30,
                    'target_category' => 'Filtros y Purificación',
                    'material' => 'plástico',
                    'dimensions' => '3/4 pulgada',
                ],
                [
                    'name' => 'Conector de codo 25 mm',
                    'description' => 'Conector para unión de tuberías en ángulo de 90 grados',
                    'amount' => 100,
                    'target_category' => 'Tuberías y Conexiones',
                    'material' => 'PVC',
                    'dimensions' => '25 mm',
                ],
            ];

            foreach ($items as $item) {
                $categoryId = $localCategories[$item['target_category']] ?? null;

                if (!$categoryId) {
                    $this->command->error("Categoría no encontrada: {$item['target_category']} para localidad {$localityId}");
                    continue;
                }

                $inventoryData[] = [
                    'locality_id' => $localityId,
                    'created_by' => $faker->randomElement($userIds),
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'amount' => $item['amount'],
                    'inventory_category_id' => $categoryId,
                    'material' => $item['material'],
                    'dimensions' => $item['dimensions'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        foreach (array_chunk($inventoryData, 50) as $chunk) {
            DB::table('inventory')->insert($chunk);
        }
    }
}
