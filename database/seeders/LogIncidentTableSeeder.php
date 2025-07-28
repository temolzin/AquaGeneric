<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LogIncident;

class LogIncidentTableSeeder extends Seeder
{
    public function run()
    {
        $logs = [
            [
                'locality_id' => 1,
                'created_by' => 2,
                'employee_id' => 1,
                'status' => 'Proceso',
                'description' => 'Datos de prueba',
            ],
            [
                'locality_id' => 2,
                'created_by' => 3,
                'employee_id' => 1,
                'status' => 'Terminado',
                'description' => 'Todo se terminó en dos semanas',
            ],
            [
                'locality_id' => 3,
                'created_by' => 2,
                'employee_id' => 1,
                'status' => 'Proceso',
                'description' => 'Se investiga las fuga',
            ],
            [
                'locality_id' => 1,
                'created_by' => 3,
                'employee_id' => 1,
                'status' => 'Terminado',
                'description' => 'No había ninguna fuga',
            ],
            [
                'locality_id' => 1,
                'created_by' => 3,
                'employee_id' => 1,
                'status' => 'Proceso',
                'description' => 'Se hacen pruebas',
            ],
        ];

        foreach ($logs as $log) {
            LogIncident::updateOrCreate(
                [
                    'status' => $log['status'],
                    'description' => $log['description'],
                    'locality_id' => $log['locality_id'],
                ],
                [
                    'employee_id' => $log['employee_id'],
                    'created_by' => $log['created_by'],
                    'updated_at' => now(),
                ]
            );
        }
    }
}
