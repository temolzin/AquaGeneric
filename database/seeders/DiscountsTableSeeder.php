<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Discount;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Discount::truncate();

        Discount::insert([
            [
                'locality_id' => null,
                'name' => 'Adulto Mayor',
                'description' => 'Descuento para personas de la tercera edad.',
                'percentage' => 50,
                'color' => '13',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'locality_id' => 1,
                'name' => 'Jubilados',
                'description' => 'Descuento para jubilados.',
                'percentage' => 40,
                'color' => '0',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'locality_id' => 1,
                'name' => 'Personas con Discapacidad',
                'description' => 'Apoyo para personas con discapacidad.',
                'percentage' => 35,
                'color' => '10', 
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'locality_id' => 1,
                'name' => 'Convenio Especial',
                'description' => 'Convenio autorizado por el administrador.',
                'percentage' => 25,
                'color' => '4',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'locality_id' => 1,
                'name' => 'Empleado',
                'description' => 'Descuento para empleados.',
                'percentage' => 20,
                'color' => '1', 
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'locality_id' => 1,
                'name' => 'Programa Social',
                'description' => 'Beneficiarios de programas sociales.',
                'percentage' => 15,
                'color' => '6', 
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'locality_id' => 1,
                'name' => 'Apoyo Municipal',
                'description' => 'Descuento autorizado por el municipio.',
                'percentage' => 10,
                'color' => '14',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
