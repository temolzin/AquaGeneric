<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ExpenseType;
use App\Models\User;

class ExpenseTypeSeeder extends Seeder
{
    public function run()
    {
        $localityIds = DB::table('localities')->pluck('id')->toArray();

        if (empty($localityIds)) {
            $this->command->error('No localities found. Skipping expense types seeding.');
            return;
        }

        $adminUserId = DB::table('users')
            ->where('email', 'jose@gmail.com')
            ->value('id');

        ExpenseType::updateOrCreate(
            [
                'name' => 'Operación Administrativa',
                'locality_id' => null,
            ],
            [
                'description' => 'Costos operativos y administrativos generales sin asignación a una localidad específica.',
                'color'       => color(16),
                'created_by'  => $adminUserId,
                'created_at'  => now(),
            ]
        );

        $baseTypes = [
            [
                'name' => 'Productos Químicos',
                'description' => 'Cloro, sulfato de aluminio y productos para tratamiento de agua',
                'color' => color(9)
            ],
            [
                'name' => 'Mantenimiento de Equipos', 
                'description' => 'Reparación y mantenimiento de bombas, tuberías y medidores',
                'color' => color(13)
            ],
            [
                'name' => 'Materiales y Suministros',
                'description' => 'Tuberías, conexiones, herramientas y equipo de protección',
                'color' => color(10)
            ],
            [
                'name' => 'Energía Eléctrica',
                'description' => 'Consumo eléctrico de bombas y equipos de tratamiento', 
                'color' => color(12)
            ],
            [
                'name' => 'Combustible y Transporte',
                'description' => 'Gasolina para vehículos de operación y mantenimiento',
                'color' => color(1)
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

            foreach ($baseTypes as $type) {
                ExpenseType::updateOrCreate(
                    [
                        'name' => $type['name'],
                        'locality_id' => $localityId
                    ],
                    [
                        'description' => $type['description'],
                        'color' => $type['color'],
                        'created_by' => collect($userIds)->random(),
                        'created_at' => now(),
                    ]
                );
            }
        }
    }
}
