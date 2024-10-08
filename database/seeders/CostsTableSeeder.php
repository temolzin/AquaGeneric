<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('costs')->insert([
            [
                'locality_id' => 1,
                'created_by' => 2,
                'category' => 'Publico en general',
                'price' => 150.00,
                'description' => 'Tarifa para publico en general',
            ],
            [
                'locality_id' => 2,
                'created_by' => 3,
                'category' => 'Adultos Mayores',
                'price' => 100.00,
                'description' => 'Tarifa para Adultos Mayores que cuenten con INAPAM',
            ],
            [
                'locality_id' => 3,
                'created_by' => 2,
                'category' => 'Usuarios Nuevos',
                'price' => 120.00,
                'description' => 'Tarifa para Usuarios Nuevos',
            ],
            [
                'locality_id' => 1,
                'created_by' => 3,
                'category' => 'Madres Solteras',
                'price' => 100.00,
                'description' => 'Tarifas para Madres Solteras',
            ],
        ]);
    }
}
