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
                'name' => 'Eléctrica',
                'description' => 'Problemas con instalación o alumbrado eléctrico.',
                'created_by' => 3,
                'locality_id' => 1,
            ],
            [
                'name' => 'Plomería',
                'description' => 'Fugas, tuberías dañadas o problemas de agua.',
                'created_by' => 3,
                'locality_id' => 1,
            ],
            [
                'name' => 'Infraestructura',
                'description' => 'Daños estructurales como techos, paredes o pisos.',
                'created_by' => 3,
                'locality_id' => 1,
            ],
            [
                'name' => 'Tecnología',
                'description' => 'Fallas con equipo de cómputo, redes o sistemas.',
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
                    'created_by' => $category['created_by'],
                    'updated_at' => now(),
                ]
            );
        }
    }
}
