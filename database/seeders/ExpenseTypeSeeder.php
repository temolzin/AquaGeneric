<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseTypeSeeder extends Seeder
{
    public function run()
    {
        $existingCount = DB::table('expense_types')->count();
        
        if ($existingCount > 0) {
            return;
        }

        $localities = DB::table('localities')->pluck('id')->toArray();
        $users = DB::table('users')->pluck('id')->toArray();

        if (empty($localities) || empty($users)) {
            $this->command->error('No localities or users found. Skipping expense types seeding.');
            return;
        }

        $baseTypes = [
            [
                'name' => 'Productos Químicos',
                'description' => 'Cloro, sulfato de aluminio y productos para tratamiento de agua',
                'color' => '#3498db'
            ],
            [
                'name' => 'Mantenimiento de Equipos', 
                'description' => 'Reparación y mantenimiento de bombas, tuberías y medidores',
                'color' => '#e74c3c'
            ],
            [
                'name' => 'Materiales y Suministros',
                'description' => 'Tuberías, conexiones, herramientas y equipo de protección',
                'color' => '#2ecc71'
            ],
            [
                'name' => 'Energía Eléctrica',
                'description' => 'Consumo eléctrico de bombas y equipos de tratamiento', 
                'color' => '#f39c12'
            ],
            [
                'name' => 'Combustible y Transporte',
                'description' => 'Gasolina para vehículos de operación y mantenimiento',
                'color' => '#9b59b6'
            ]
        ];

        $expenseTypesData = [];

        $expenseTypesData[] = [
            'name' => 'Operación Administrativa',
            'description' => 'Costos operativos y administrativos generales sin asignación a una localidad específica.',
            'color' => '#d800b4',
            'locality_id' => null,
            'created_by' => $users[0],
            'created_at' => now(),
            'updated_at' => now(),
        ];

        foreach ($localities as $localityId) {
            foreach ($baseTypes as $type) {
                $expenseTypesData[] = [
                    'name' => $type['name'],
                    'description' => $type['description'],
                    'color' => $type['color'],
                    'locality_id' => $localityId,
                    'created_by' => $users[0],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('expense_types')->insert($expenseTypesData);
    }
}
