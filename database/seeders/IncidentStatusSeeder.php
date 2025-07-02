<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IncidentStatusSeeder extends Seeder
{
    public function run()
    {
        DB::table('incident_statuses')->insert([
            [
                'status' => 'Pendiente',
                'description' => 'Incidencia en proceso de ser atendida',
                'created_by' => 3,
                'locality_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status' => 'En progreso',
                'description' => 'Incidencia en proceso de resolución',
                'created_by' => 3,
                'locality_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status' => 'Terminada',
                'description' => 'Incidencia resuelta y cerrada.',
                'created_by' => 3,
                'locality_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
