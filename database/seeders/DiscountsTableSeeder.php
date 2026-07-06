<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Discount;
use Illuminate\Support\Facades\DB;

class DiscountsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('discounts')->delete();

        $discounts = [
            [
                'locality_id' => 1,
                'created_by' => 1,
                'name' => 'Adulto Mayor',
                'percentage' => 50,
                'color' => '#3498db',
                'description' => 'Descuento para personas de la tercera edad.',
            ],
            [
                'locality_id' => 1,
                'created_by' => 1,
                'name' => 'Jubilados',
                'percentage' => 40,
                'color' => '#e74c3c',
                'description' => 'Descuento para jubilados.',
            ],
            [
                'locality_id' => 1,
                'created_by' => 1,
                'name' => 'Personas con Discapacidad',
                'percentage' => 35,
                'color' => '#f39c12',
                'description' => 'Apoyo para personas con discapacidad.',
            ],
            [
                'locality_id' => 1,
                'created_by' => 1,
                'name' => 'Convenio Especial',
                'percentage' => 25,
                'color' => '#2ecc71',
                'description' => 'Convenio autorizado por el administrador.',
            ],
            [
                'locality_id' => 1,
                'created_by' => 1,
                'name' => 'Empleado',
                'percentage' => 20,
                'color' => '#3498db',
                'description' => 'Descuento para empleados.',
            ],
            [
                'locality_id' => 1,
                'created_by' => 1,
                'name' => 'Programa Social',
                'percentage' => 15,
                'color' => '#f39c12',
                'description' => 'Beneficiarios de programas sociales.',
            ],
            [
                'locality_id' => 1,
                'created_by' => 1,
                'name' => 'Apoyo Municipal',
                'percentage' => 10,
                'color' => '#e74c3c',
                'description' => 'Descuento autorizado por el municipio.',
            ],
        ];

        foreach ($discounts as $discount) {
            Discount::create($discount);
        }
    }
}
