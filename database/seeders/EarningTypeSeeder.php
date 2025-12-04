<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EarningType;
use App\Models\Locality;
use App\Models\User;

class EarningTypeSeeder extends Seeder
{
    public function run()
    {
        $localities = Locality::pluck('id')->toArray();
        $users = User::pluck('id')->toArray();

        if (empty($localities) || empty($users)) {
            $this->command->error('No localities or users found. Skipping earning types seeding.');
            return;
        }

        $userId = $users[0];

        EarningType::updateOrCreate(
            [
                'name' => 'Operación Administrativa',
                'locality_id' => null,
            ],
            [
                'description' => 'Ingreso operativos y administrativos generales sin asignación a una localidad específica.',
                'color' => color(16),
                'created_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
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

        foreach ($localities as $localityId) {
            foreach ($baseTypes as $type) {
                EarningType::updateOrCreate(
                    [
                        'name' => $type['name'],
                        'locality_id' => $localityId
                    ],
                    [
                        'description' => $type['description'],
                        'color' => $type['color'],
                        'created_by' => $userId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
