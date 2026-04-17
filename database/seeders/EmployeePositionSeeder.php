<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\EmployeePosition;
use App\Models\User;

class EmployeePositionSeeder extends Seeder
{
    public function run()
    {
        $localityIds = DB::table('localities')->pluck('id')->toArray();

        if (empty($localityIds)) {
            $this->command->error('No se encontraron localidades. Se omitirá la siembra de cargos de empleados.');
            return;
        }

        $adminUserId = DB::table('users')
            ->where('email', 'jose@gmail.com')
            ->value('id');

        // Crear posición global
        EmployeePosition::updateOrCreate(
            [
                'name' => 'Administrador',
                'locality_id' => null,
            ],
            [
                'description' => 'Posición administrativa general sin asignación a una localidad específica.',
                'color'       => color(16),
                'created_by'  => $adminUserId,
                'created_at'  => now(),
            ]
        );

        $rolesDisponibles = [
            [
                'name' => 'Recepcionista', 
                'description' => 'Encargado de atención al cliente y recepción de solicitudes.',
                'color' => color(13)
            ],
            [
                'name' => 'Encargado',
                'description' => 'Responsable de operaciones y supervisión de actividades.',
                'color' => color(10)
            ],
            [
                'name' => 'Seguridad',
                'description' => 'Responsable de la seguridad y vigilancia de las instalaciones.', 
                'color' => color(6)
            ]
        ];

        foreach ($localityIds as $localityId) {
            $userIds = DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->whereIn('roles.name', [User::ROLE_SUPERVISOR, User::ROLE_SECRETARY])
                ->where('users.locality_id', $localityId)
                ->distinct()
                ->pluck('users.id')
                ->toArray();

            if (empty($userIds)) {
                continue;
            }

            foreach ($rolesDisponibles as $rol) {
                EmployeePosition::updateOrCreate(
                    [
                        'name' => $rol['name'],
                        'locality_id' => $localityId
                    ],
                    [
                        'description' => $rol['description'],
                        'color' => $rol['color'],
                        'created_by' => collect($userIds)->random(),
                        'created_at' => now(),
                    ]
                );
            }
        }
    }
}
