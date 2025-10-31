<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IncidentStatus;

class IncidentStatusSeeder extends Seeder
{
    public function run()
    {
        $statuses = [
            [
                'status' => 'Pendiente',
                'description' => 'Incidencia en proceso de ser atendida',
                'color' => '#e74c3c', 
                'created_by' => 3,
                'locality_id' => 1,
            ],
            [
                'status' => 'En progreso', 
                'description' => 'Incidencia en proceso de resolución',
                'color' => '#f39c12', 
                'created_by' => 3,
                'locality_id' => 1,
            ],
            [
                'status' => 'Terminada',
                'description' => 'Incidencia resuelta y cerrada.',
                'color' => '#2ecc71', 
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
                    'color' => $status['color'],
                    'created_by' => $status['created_by'],
                    'updated_at' => now(),
                ]
            );
        }
    }
}
