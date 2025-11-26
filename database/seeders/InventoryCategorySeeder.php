<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\InventoryCategory;
use App\Models\Locality;
use App\Models\User;

class InventoryCategorySeeder extends Seeder
{
    public function run()
    {
        $firstUser = User::first()?->id ?? 1;
        InventoryCategory::updateOrCreate(
            [
                'name' => 'Recursos Generales',
                'locality_id' => null,
            ],
            [
                'description' => 'Agrupa los recursos de inventario utilizados de forma general en la organización.',
                'color' => color(2),
                'created_by' => $firstUser,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $localities = Locality::pluck('id')->toArray();
        $users = User::pluck('id')->toArray();

        if (empty($localities) || empty($users)) {
            $this->command->error('No localities or users found. Skipping inventory categories seeding.');
            return;
        }

        $baseCategories = [
            [
                'name' => 'Medidores de Agua',
                'description' => 'Medidores residenciales, comerciales e industriales para control de consumo',
                'color' => color(9)
            ],
            [
                'name' => 'Tuberías y Conexiones', 
                'description' => 'Tuberías PVC, cobre, conexiones, codos y uniones para red de distribución',
                'color' => color(13)
            ],
            [
                'name' => 'Válvulas y Reguladores',
                'description' => 'Válvulas de control, compuerta, globo y reguladores de presión',
                'color' => color(10)
            ],
            [
                'name' => 'Bombas y Motores', 
                'description' => 'Bombas de agua, motores eléctricos y sistemas de bombeo',
                'color' => color(12)
            ],
            [
                'name' => 'Filtros y Purificación',
                'description' => 'Filtros de sedimentos, carbón activado y sistemas de purificación',
                'color' => color(1)
            ]
        ];

        foreach ($localities as $localityId) {
            foreach ($baseCategories as $category) {
                InventoryCategory::updateOrCreate(
                    [
                        'name' => $category['name'],
                        'locality_id' => $localityId,
                    ],
                    [
                        'description' => $category['description'],
                        'color' => $category['color'],
                        'created_by' => $users[0],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
