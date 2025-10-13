<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseTypeSeeder extends Seeder
{
    public function run()
    {
        $localityIds = DB::table('localities')->pluck('id')->toArray();
        $userIds = DB::table('users')->pluck('id')->toArray();

        if (empty($localityIds) || empty($userIds)) {
            throw new \Exception('No hay IDs disponibles en las tablas localities o users.');
        }

        $expenseTypesData = [];

        foreach ($localityIds as $localityId) {
            $expenseTypesData[] = [
                'name' => 'Productos Químicos',
                'description' => 'Cloro, sulfato de aluminio y productos para tratamiento de agua',
                'color' => '#3498db',
                'locality_id' => $localityId,
                'created_by' => $userIds[0],
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            $expenseTypesData[] = [
                'name' => 'Mantenimiento de Equipos',
                'description' => 'Reparación y mantenimiento de bombas, tuberías y medidores',
                'color' => '#e74c3c',
                'locality_id' => $localityId,
                'created_by' => $userIds[0],
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            $expenseTypesData[] = [
                'name' => 'Materiales y Suministros',
                'description' => 'Tuberías, conexiones, herramientas y equipo de protección',
                'color' => '#2ecc71',
                'locality_id' => $localityId,
                'created_by' => $userIds[0],
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            $expenseTypesData[] = [
                'name' => 'Energía Eléctrica',
                'description' => 'Consumo eléctrico de bombas y equipos de tratamiento',
                'color' => '#f39c12',
                'locality_id' => $localityId,
                'created_by' => $userIds[0],
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            $expenseTypesData[] = [
                'name' => 'Combustible y Transporte',
                'description' => 'Gasolina para vehículos de operación y mantenimiento',
                'color' => '#9b59b6',
                'locality_id' => $localityId,
                'created_by' => $userIds[0],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach ($expenseTypesData as $data) {
            DB::table('expense_types')->updateOrInsert(
                [
                    'name' => $data['name'],
                    'locality_id' => $data['locality_id'],
                ],
                $data
            );
        }
    }
}
