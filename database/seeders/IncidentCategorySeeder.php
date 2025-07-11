<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IncidentCategorySeeder extends Seeder
{
    public function run()
    {
        DB::table('incident_categories')->insert([
            [
                'name' => 'Eléctrica',
                'description' => 'Problemas con instalación o alumbrado eléctrico.',
                'created_by' => 3,
                'locality_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Plomería',
                'description' => 'Fugas, tuberías dañadas o problemas de agua.',
                'created_by' => 3,
                'locality_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Infraestructura',
                'description' => 'Daños estructurales como techos, paredes o pisos.',
                'created_by' => 3,
                'locality_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tecnología',
                'description' => 'Fallas con equipo de cómputo, redes o sistemas.',
                'created_by' => 3,
                'locality_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
