<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Incident;
use App\Models\IncidentStatus;
use Carbon\Carbon;

class IncidentSeeder extends Seeder
{
    public function run()
    {
        $this->call(IncidentStatusSeeder::class);

        $pendienteId = IncidentStatus::where('status', 'Pendiente')->value('id');
        $enProgresoId = IncidentStatus::where('status', 'En progreso')->value('id');
        $terminadaId = IncidentStatus::where('status', 'Terminada')->value('id');

        $incidents = [
            [
                'name' => 'Falla en iluminación',
                'description' => 'No funcionan las luces del pasillo principal.',
                'status_id' => $pendienteId,
                'start_date' => Carbon::now()->subDays(3)->toDateString(),
                'category_id' => 3,
                'locality_id' => 1,
                'created_by' => 3,
            ],
            [
                'name' => 'Fuga en baño',
                'description' => 'Se reporta fuga de agua en el baño de hombres.',
                'status_id' => $enProgresoId,
                'start_date' => Carbon::now()->subDays(2)->toDateString(),
                'category_id' => 4,
                'locality_id' => 1,
                'created_by' => 3,
            ],
            [
                'name' => 'Ventana rota',
                'description' => 'Ventana rota en la oficina',
                'status_id' => $terminadaId,
                'start_date' => Carbon::now()->subDays(5)->toDateString(),
                'category_id' => 3,
                'locality_id' => 1,
                'created_by' => 3,
            ],
            [
                'name' => 'Puerta dañada',
                'description' => 'Puerta principal con bisagras flojas, requiere reparación urgente.',
                'status_id' => $pendienteId,
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
                    'status_id' => $incident['status_id'],
                    'start_date' => $incident['start_date'],
                    'category_id' => $incident['category_id'],
                    'created_by' => $incident['created_by'],
                    'updated_at' => now(),
                ]
            );
        }
    }
}
