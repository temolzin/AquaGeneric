<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryCategorySeeder extends Seeder
{
    public function run()
    {
        $existingCount = DB::table('inventory_categories')->count();
        
        if ($existingCount > 0) {
            return;
        }

        $localities = DB::table('localities')->pluck('id')->toArray();
        $users = DB::table('users')->pluck('id')->toArray();

        if (empty($localities) || empty($users)) {
            $this->command->error('No localities or users found. Skipping inventory categories seeding.');
            return;
        }

        $baseCategories = [
            [
                'name' => 'Medidores de Agua',
                'description' => 'Medidores residenciales, comerciales e industriales para control de consumo',
                'color' => '#3498db'
            ],
            [
                'name' => 'Tuberías y Conexiones', 
                'description' => 'Tuberías PVC, cobre, conexiones, codos y uniones para red de distribución',
                'color' => '#e74c3c'
            ],
            [
                'name' => 'Válvulas y Reguladores',
                'description' => 'Válvulas de control, compuerta, globo y reguladores de presión',
                'color' => '#2ecc71'
            ],
            [
                'name' => 'Bombas y Motores', 
                'description' => 'Bombas de agua, motores eléctricos y sistemas de bombeo',
                'color' => '#f39c12'
            ],
            [
                'name' => 'Filtros y Purificación',
                'description' => 'Filtros de sedimentos, carbón activado y sistemas de purificación',
                'color' => '#9b59b6'
            ]
        ];

        $inventoryCategoriesData = [];

        foreach ($localities as $localityId) {
            foreach ($baseCategories as $category) {
                $inventoryCategoriesData[] = [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'color' => $category['color'],
                    'locality_id' => $localityId,
                    'created_by' => $users[0],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('inventory_categories')->insert($inventoryCategoriesData);
    }
}
