<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IncidentSeeder extends Seeder
{
    public function run()
    {
        DB::table('incidents')->insert([
            [
                'name' => 'Falla en iluminación',
                'description' => 'No funcionan las luces del pasillo principal.',
                'status' => 'Pendiente',
                'start_date' => Carbon::now()->subDays(3)->toDateString(),
                'category_id' => 3,
                'locality_id' => 1,
                'created_by' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fuga en baño',
                'description' => 'Se reporta fuga de agua en el baño de hombres.',
                'status' => 'En progreso',
                'start_date' => Carbon::now()->subDays(2)->toDateString(),
                'category_id' => 4,
                'locality_id' => 1,
                'created_by' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ventana rota',
                'description' => 'Ventana rota en la oficina',
                'status' => 'Terminada',
                'start_date' => Carbon::now()->subDays(5)->toDateString(),
                'category_id' => 3,
                'locality_id' => 1,
                'created_by' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Puerta dañada',
                'description' => 'Puerta principal con bisagras flojas, requiere reparación urgente.',
                'status' => 'Pendiente',
                'start_date' => Carbon::now()->subDays(1)->toDateString(),
                'category_id' => 3,
                'locality_id' => 1,
                'created_by' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
