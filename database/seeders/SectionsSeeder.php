<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Section;
use App\Models\Locality;

class SectionsSeeder extends Seeder
{
    public function run()
    {
        $colors = ['purple', 'light-blue', 'orange', 'lime', 'navy', 'olive', 'secondary'];

        $localities = Locality::all(); 

        foreach ($localities as $locality) {
            for ($i = 1; $i <= 4; $i++) {
                Section::updateOrCreate(
                    [
                        'locality_id' => $locality->id,
                        'name' => 'Sección ' . $i . ' - Localidad ' . $locality->id,
                    ],
                    [
                        'created_by' => 1,
                        'zip_code' => str_pad(55000 + ($locality->id * 10) + $i, 5, '0', STR_PAD_LEFT),
                        'color' => $colors[$i % count($colors)],
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
