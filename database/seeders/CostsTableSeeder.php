<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cost;

class CostsTableSeeder extends Seeder
{
    public function run()
    {
        $costs = [
            [
                'locality_id' => 1,
                'created_by' => 2,
                'category' => 'Publico en general',
                'price' => 150.00,
                'description' => 'Tarifa para publico en general',
            ],
            [
                'locality_id' => 2,
                'created_by' => 3,
                'category' => 'Adultos Mayores',
                'price' => 100.00,
                'description' => 'Tarifa para Adultos Mayores que cuenten con INAPAM',
            ],
            [
                'locality_id' => 3,
                'created_by' => 2,
                'category' => 'Usuarios Nuevos',
                'price' => 120.00,
                'description' => 'Tarifa para Usuarios Nuevos',
            ],
            [
                'locality_id' => 1,
                'created_by' => 3,
                'category' => 'Madres Solteras',
                'price' => 100.00,
                'description' => 'Tarifas para Madres Solteras',
            ],
        ];

        foreach ($costs as $cost) {
            Cost::updateOrCreate(
                [
                    'locality_id' => $cost['locality_id'],
                    'category' => $cost['category'],
                ],
                [
                    'price' => $cost['price'],
                    'description' => $cost['description'],
                    'created_by' => $cost['created_by'],
                    'updated_at' => now(),
                ]
            );
        }
    }
}
