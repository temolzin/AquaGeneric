<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Section;

class SectionsTableSeeder extends Seeder
{
    public function run()
    {
        $sections = [
            [
                'locality_id' => 1,
                'created_by' => 1,
                'name' => 'Sección A',
                'zip_code' => '55001',
                'color' => 'indigo',
            ],
            [
                'locality_id' => 1,
                'created_by' => 1,
                'name' => 'Sección B',
                'zip_code' => '55002',
                'color' => 'light-blue',
            ],
            [
                'locality_id' => 2,
                'created_by' => 1,
                'name' => 'Zona Sur',
                'zip_code' => '55003',
                'color' => 'navy',
            ],
            [
                'locality_id' => 3,
                'created_by' => 1,
                'name' => 'Zona Centro',
                'zip_code' => '55004',
                'color' => 'lime',
            ],
            [
                'locality_id' => 4,
                'created_by' => 1,
                'name' => 'Zona Norte',
                'zip_code' => '55005',
                'color' => 'teal',
            ],
        ];

        foreach ($sections as $section) {
            Section::updateOrCreate(
                [
                    'locality_id' => $section['locality_id'],
                    'name' => $section['name'],
                ],
                [
                    'created_by' => $section['created_by'],
                    'zip_code' => $section['zip_code'],
                    'color' => $section['color'],
                    'updated_at' => now(),
                ]
            );
        }
    }
}
