<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpenseType;

class ExpenseTypeSeeder extends Seeder
{
    public function run()
    {
        $types = [
            [
                'name' => 'Productos Químicos',
                'description' => 'Cloro, sulfato de aluminio y productos para tratamiento de agua',
                'color' => '#3498db',
                'created_by' => 1, 
                'locality_id' => 1,
            ],
            [
                'name' => 'Mantenimiento de Equipos',
                'description' => 'Reparación y mantenimiento de bombas, tuberías y medidores',
                'color' => '#e74c3c',
                'created_by' => 1,
                'locality_id' => 1,
            ],
            [
                'name' => 'Materiales y Suministros',
                'description' => 'Tuberías, conexiones, herramientas y equipo de protección',
                'color' => '#2ecc71',
                'created_by' => 1,
                'locality_id' => 1,
            ],
        ];

        foreach ($types as $type) {
            ExpenseType::updateOrCreate(
                [
                    'name' => $type['name'],
                    'locality_id' => $type['locality_id'],
                ],
                [
                    'description' => $type['description'],
                    'color' => $type['color'],
                    'created_by' => $type['created_by'],
                    'updated_at' => now(),
                ]
            );
        }
    }
}
