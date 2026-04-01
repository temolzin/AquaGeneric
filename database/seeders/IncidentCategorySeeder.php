<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IncidentCategory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class IncidentCategorySeeder extends Seeder
{
    public function run($localityId = null)
    {
        $adminUserId = DB::table('users')
            ->where('email', 'jose@gmail.com')
            ->value('id');

        IncidentCategory::updateOrCreate(
            [
                'name' => 'Mantenimiento General',
                'locality_id' => null,
            ],
            [
                'description' => 'Trabajos generales de reparación o conservación del sistema de agua.',
                'color' => color(9),
                'created_by' => $adminUserId,
                'created_at' => now(),
            ]
        );

        $localityIds = $localityId ? [$localityId] : DB::table('localities')->pluck('id')->toArray();

        if (empty($localityIds)) {
            return;
        }

        $baseCategories = [
            [
                'name' => 'Eléctrica',
                'description' => 'Problemas con instalación o alumbrado eléctrico.',
                'color' => color(3)
            ],
            [
                'name' => 'Plomería',
                'description' => 'Fugas, tuberías dañadas o problemas de agua.',
                'color' => color(0)
            ],
            [
                'name' => 'Infraestructura',
                'description' => 'Daños estructurales como techos, paredes o pisos.',
                'color' => color(4)
            ],
            [
                'name' => 'Tecnología',
                'description' => 'Fallas con equipo de cómputo, redes o sistemas.',
                'color' => color(1)
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

            foreach ($baseCategories as $category) {
                IncidentCategory::updateOrCreate(
                    [
                        'name' => $category['name'],
                        'locality_id' => $lId
                    ],
                    [
                        'description' => $category['description'],
                        'color' => $category['color'],
                        'created_by' => collect($userIds)->random(),
                        'created_at' => now(),
                    ]
                );
            }
        }
    }
}
