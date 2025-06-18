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
                'reponsible' => '1',
                'status' => 'Proceso',
                'description' => 'Datos de prueba',
            ],
            [
                'locality_id' => 2,
                'created_by' => 3,
                'reponsible' => '1',
                'status' => 'Terminado',
                'description' => 'Todo se termino en dos semanas',
            ],
            [
                'locality_id' => 3,
                'created_by' => 2,
                'reponsible' => '1',
                'status' => 'Proceso',
                'description' => 'Se investiga las fuga',
            ],
            [
                'locality_id' => 1,
                'created_by' => 3,
                'reponsible' => '1',
                'status' => 'Termindo',
                'description' => 'No habia ninguna fuga',
            ],
            [
                'locality_id' => 1,
                'created_by' => 3,
                'reponsible' => '1',
                'status' => 'Proceso',
                'description' => 'Se hacen pruebas',
            ],
        ]);
    }
}
