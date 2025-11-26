<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IncidentCategory;

class IncidentCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Mantenimiento  General',
                'description' => 'Trabajos generales de reparación o conservación del sistema de agua.',
                'color' => color(9),
                'created_by' => 3,
                'locality_id' => null,
            ],
            [
                'name' => 'Eléctrica',
                'description' => 'Problemas con instalación o alumbrado eléctrico.',
                'color' => color(3),
                'created_by' => 3,
                'locality_id' => 1,
            ],
            [
                'name' => 'Plomería',
                'description' => 'Fugas, tuberías dañadas o problemas de agua.',
                'color' => color(0),
                'created_by' => 3,
                'locality_id' => 1,
            ],
            [
                'name' => 'Infraestructura',
                'description' => 'Daños estructurales como techos, paredes o pisos.',
                'color' => color(4),
                'created_by' => 3,
                'locality_id' => 1,
            ],
            [
                'name' => 'Tecnología',
                'description' => 'Fallas con equipo de cómputo, redes o sistemas.',
                'color' => color(1),
                'created_by' => 3,
                'locality_id' => 1,
            ],
        ];

        foreach ($categories as $category) {
            IncidentCategory::updateOrCreate(
                [
                    'name' => $category['name'],
                    'locality_id' => $category['locality_id'],
                ],
                [
                    'description' => $category['description'],
                    'color' => $category['color'],
                    'created_by' => $category['created_by'],
                    'updated_at' => now(),
                ]
            );
        }
    }
}
