<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\EarningType;
use App\Models\User;

class EarningTypeSeeder extends Seeder
{
    public function run()
    {
        $localityIds = DB::table('localities')->pluck('id')->toArray();

        if (empty($localityIds)) {
            $this->command->error('No localities found. Skipping earning types seeding.');
            return;
        }

        $adminUserId = DB::table('users')
            ->where('email', 'jose@gmail.com')
            ->value('id');

        EarningType::updateOrCreate(
            [
                'name' => 'Operación Administrativa',
                'locality_id' => null,
            ],
            [
                'description' => 'Ingreso operativos y administrativos generales sin asignación a una localidad específica.',
                'color' => color(16),
                'created_by' => $adminUserId,
                'created_at' => now(),
            ]
        );

        $baseTypes = [
            [
                'name' => 'Donativos y Contribuciones',
                'description' => 'Donaciones en efectivo o especie recibidas de personas, empresas u organizaciones',
                'color' => color(9)
            ],
            [
                'name' => 'Regalías y Derechos',
                'description' => 'Ingresos por conceptos de regalías, derechos de autor o licencias',
                'color' => color(13)
            ],
            [
                'name' => 'Venta de Servicios',
                'description' => 'Ingresos por prestación de servicios técnicos, profesionales o especializados',
                'color' => color(10)
            ],
            [
                'name' => 'Subvenciones y Aportes',
                'description' => 'Recursos recibidos de entidades públicas o privadas para proyectos específicos',
                'color' => color(12)
            ],
            [
                'name' => 'Rendimientos Financieros',
                'description' => 'Intereses, dividendos y ganancias por inversiones financieras',
                'color' => color(1)
            ],
            [
                'name' => 'Cuotas y Membresías',
                'description' => 'Ingresos por cuotas de afiliación, membresías o suscripciones',
                'color' => color(4)
            ],
            [
                'name' => 'Multas y Recargos',
                'description' => 'Ingresos por conceptos de multas, recargos o sanciones administrativas',
                'color' => color(14)
            ],
            [
                'name' => 'Alquileres y Arrendamientos',
                'description' => 'Ingresos por alquiler de propiedades, equipos o espacios físicos',
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

            foreach ($baseTypes as $type) {
                EarningType::updateOrCreate(
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
