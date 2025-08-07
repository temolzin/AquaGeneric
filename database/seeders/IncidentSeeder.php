<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Incident;
use Carbon\Carbon;

class IncidentSeeder extends Seeder
{
    public function run()
    {
        $incidents = [
            [
                'name' => 'Falla en iluminación',
                'description' => 'No funcionan las luces del pasillo principal.',
                'status' => 'Pendiente',
                'start_date' => Carbon::now()->subDays(3)->toDateString(),
                'category_id' => 3,
                'locality_id' => 1,
                'created_by' => 3,
            ],
            [
                'name' => 'Fuga en baño',
                'description' => 'Se reporta fuga de agua en el baño de hombres.',
                'status' => 'En progreso',
                'start_date' => Carbon::now()->subDays(2)->toDateString(),
                'category_id' => 4,
                'locality_id' => 1,
                'created_by' => 3,
            ],
            [
                'name' => 'Ventana rota',
                'description' => 'Ventana rota en la oficina',
                'status' => 'Terminada',
                'start_date' => Carbon::now()->subDays(5)->toDateString(),
                'category_id' => 3,
                'locality_id' => 1,
                'created_by' => 3,
            ],
            [
                'name' => 'Puerta dañada',
                'description' => 'Puerta principal con bisagras flojas, requiere reparación urgente.',
                'status' => 'Pendiente',
                'start_date' => Carbon::now()->subDays(1)->toDateString(),
                'category_id' => 3,
                'locality_id' => 1,
                'created_by' => 3,
            ],
        ];

        foreach ($incidents as $incident) {
            Incident::updateOrCreate(
                [
                    'name' => $incident['name'],
                    'locality_id' => $incident['locality_id'],
                ],
                [
                    'description' => $incident['description'],
                    'status' => $incident['status'],
                    'start_date' => $incident['start_date'],
                    'category_id' => $incident['category_id'],
                    'created_by' => $incident['created_by'],
                    'updated_at' => now(),
                ]
            );
        }
    }
}
