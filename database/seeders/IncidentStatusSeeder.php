<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IncidentStatus;
use App\Models\Locality;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class IncidentStatusSeeder extends Seeder
{
    public function run()
    {
        $statusConfigs = [
            [
                'status' => 'Reportada',
                'description' => 'Incidencia registrada y en espera de ser evaluada o asignada para su atención.',
                'color' => color(1),
            ],
            [
                'status' => 'Pendiente',
                'description' => 'Incidencia en proceso de ser atendida',
                'color' => color(13),
            ],
            [
                'status' => 'En progreso',
                'description' => 'Incidencia en proceso de resolución',
                'color' => color(12),
            ],
            [
                'status' => 'Terminada',
                'description' => 'Incidencia resuelta y cerrada.',
                'color' => color(10),
            ],
        ];

        $localities = Locality::all();

        foreach ($localities as $locality) {
            $createdBy = $this->getUserForLocality($locality->id);
            
            foreach ($statusConfigs as $statusConfig) {
                IncidentStatus::updateOrCreate(
                    [
                        'status' => $statusConfig['status'],
                        'locality_id' => $locality->id,
                    ],
                    [
                        'description' => $statusConfig['description'],
                        'color' => $statusConfig['color'],
                        'created_by' => $createdBy,
                    ]
                );
            }
        }
    }

    private function getUserForLocality(int $localityId): ?int
    {
        return DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->whereIn('roles.name', [User::ROLE_SUPERVISOR, User::ROLE_SECRETARY])
            ->where('users.locality_id', $localityId)
            ->distinct()
            ->value('users.id');
    }
}
