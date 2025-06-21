<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LogIncidentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('log_incidents')->insert([
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
        ]);
    }
}
