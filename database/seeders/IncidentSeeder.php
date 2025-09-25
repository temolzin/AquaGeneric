<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Incident;
use Illuminate\Support\Facades\DB;
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
            $statusId = DB::table('incident_statuses')->where('status', $incident['status'])->value('id');

            if (!$statusId) {
                $statusId = DB::table('incident_statuses')->insertGetId([
                    'status' => $incident['status'],
                    'description' => null,
                    'created_by' => $incident['created_by'],
                    'locality_id' => $incident['locality_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            Incident::updateOrCreate(
                [
                    'name' => $incident['name'],
                    'locality_id' => $incident['locality_id'],
                ],
                [
                    'description' => $incident['description'],
                    'status_id' => $statusId, 
                    'start_date' => $incident['start_date'],
                    'category_id' => $incident['category_id'],
                    'created_by' => $incident['created_by'],
                    'updated_at' => now(),
                ]
            );
        }
    }
}