<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IncidentStatus; // Asegúrate de tener este modelo

class IncidentStatusSeeder extends Seeder
{
    public function run()
    {
        $statuses = [
            [
                'status' => 'Pendiente',
                'description' => 'Incidencia en proceso de ser atendida',
                'created_by' => 3,
                'locality_id' => 1,
            ],
            [
                'status' => 'En progreso',
                'description' => 'Incidencia en proceso de resolución',
                'created_by' => 3,
                'locality_id' => 1,
            ],
            [
                'status' => 'Terminada',
                'description' => 'Incidencia resuelta y cerrada.',
                'created_by' => 3,
                'locality_id' => 1,
            ],
        ];

        foreach ($statuses as $status) {
            IncidentStatus::updateOrCreate(
                [
                    'status' => $status['status'],
                    'locality_id' => $status['locality_id'],
                ],
                [
                    'description' => $status['description'],
                    'created_by' => $status['created_by'],
                    'updated_at' => now(),
                ]
            );
        }
    }
}
