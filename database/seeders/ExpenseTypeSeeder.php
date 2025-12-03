<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ExpenseType;
use App\Models\Locality;
use App\Models\User;

class ExpenseTypeSeeder extends Seeder
{
    public function run()
    {
        $localities = Locality::pluck('id')->toArray();
        $users = User::pluck('id')->toArray();

        if (empty($localities) || empty($users)) {
            $this->command->error('No localities or users found. Skipping expense types seeding.');
            return;
        }

        $userId = $users[0];

        ExpenseType::updateOrCreate(
            [
                'name' => 'Operación Administrativa',
                'locality_id' => null,
            ],
            [
                'description' => 'Costos operativos y administrativos generales sin asignación a una localidad específica.',
                'color'       => color(16),
                'created_by'  => $userId,
                'created_at'  => now(),
                'updated_at'  => now(),
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

        foreach ($localities as $localityId) {
            foreach ($baseTypes as $type) {
                ExpenseType::updateOrCreate(
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
