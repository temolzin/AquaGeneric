<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IncidentStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class IncidentStatusSeeder extends Seeder
{
    public function run($localityId = null)
    {
        $adminUserId = DB::table('users')
            ->where('email', 'jose@gmail.com')
            ->value('id');

        IncidentStatus::updateOrCreate(
            [
                'status' => 'Reportada',
                'locality_id' => null,
            ],
            [
                'description' => 'Incidencia registrada y en espera de ser evaluada o asignada para su atención.',
                'color' => color(1),
                'created_by' => $adminUserId,
                'created_at' => now(),
            ]
        );

        if ($localityId) {
            $userIds = DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->whereIn('roles.name', [User::ROLE_SUPERVISOR, User::ROLE_SECRETARY])
                ->where('users.locality_id', $localityId)
                ->distinct()
                ->pluck('users.id')
                ->toArray();

            if (!empty($userIds)) {
                IncidentStatus::updateOrCreate(
                    [
                        'status' => 'Pendiente',
                        'locality_id' => $localityId
                    ],
                    [
                        'description' => 'Incidencia en proceso de ser atendida',
                        'color' => color(13),
                        'created_by' => collect($userIds)->random(),
                        'created_at' => now(),
                    ]
                );
            }
            return;
        }

        $localityIds = DB::table('localities')->pluck('id')->toArray();

        if (empty($localityIds)) {
            return;
        }

        $baseStatuses = [
            [
                'status' => 'Pendiente',
                'description' => 'Incidencia en proceso de ser atendida',
                'color' => color(13)
            ],
            [
                'status' => 'En progreso',
                'description' => 'Incidencia en proceso de resolución',
                'color' => color(12)
            ],
            [
                'status' => 'Terminada',
                'description' => 'Incidencia resuelta y cerrada.',
                'color' => color(10)
            ]
        ];

        foreach ($localityIds as $lId) {
            $userIds = DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->whereIn('roles.name', [User::ROLE_SUPERVISOR, User::ROLE_SECRETARY])
                ->where('users.locality_id', $lId)
                ->distinct()
                ->pluck('users.id')
                ->toArray();

            if (empty($userIds)) {
                continue;
            }

            foreach ($baseStatuses as $status) {
                IncidentStatus::updateOrCreate(
                    [
                        'status' => $status['status'],
                        'locality_id' => $lId
                    ],
                    [
                        'description' => $status['description'],
                        'color' => $status['color'],
                        'created_by' => collect($userIds)->random(),
                        'created_at' => now(),
                    ]
                );
            }
        }
    }
}
