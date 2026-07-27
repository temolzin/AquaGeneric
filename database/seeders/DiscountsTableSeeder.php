<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Discount;
use App\Models\User;

class DiscountsTableSeeder extends Seeder
{
    public function run()
    {
        $localityIds = DB::table('localities')->pluck('id')->toArray();

        if (empty($localityIds)) {
            $this->command->error('No localities found. Skipping discounts seeding.');
            return;
        }

        $adminUserId = DB::table('users')
            ->where('email', 'jose@gmail.com')
            ->value('id');

        Discount::updateOrCreate(
            [
                'name' => 'Adulto Mayor',
                'locality_id' => null,
            ],
            [
                'description' => 'Descuento para personas de la tercera edad.',
                'percentage' => 35,
                'color' => color(13),
                'created_by' => $adminUserId,
                'created_at' => now(),
            ]
        );

        $baseDiscounts = [
            [
                'name' => 'Jubilados',
                'description' => 'Descuento para jubilados.',
                'percentage' => 40,
                'color' => color(0)
            ],
            [
                'name' => 'Personas con Discapacidad',
                'description' => 'Apoyo para personas con discapacidad.',
                'percentage' => 35,
                'color' => color(10)
            ],
            [
                'name' => 'Convenio Especial',
                'description' => 'Convenio autorizado por el administrador.',
                'percentage' => 25,
                'color' => color(4)
            ],
            [
                'name' => 'Empleado',
                'description' => 'Descuento para empleados.',
                'percentage' => 20,
                'color' => color(1)
            ],
            [
                'name' => 'Programa Social',
                'description' => 'Beneficiarios de programas sociales.',
                'percentage' => 15,
                'color' => color(6)
            ],
            [
                'name' => 'Apoyo Municipal',
                'description' => 'Descuento autorizado por el municipio.',
                'percentage' => 10,
                'color' => color(14)
            ]
        ];
        $localityId = DB::table('localities')->where('name', 'Smallville')->value('id');

        if (!$localityId) {
            $this->command->error('La localidad Smallville no existe.');
            return;
        }

        $supervisorId = DB::table('users')->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')->join('roles', 'model_has_roles.role_id', '=', 'roles.id')->where('roles.name', User::ROLE_SUPERVISOR)->where('users.locality_id', $localityId)->value('users.id');

        if (!$supervisorId) {
            $this->command->error('No existe un Supervisor para Smallville.');
            return;
        }

        foreach ($baseDiscounts as $discount) {
            Discount::updateOrCreate(
                [
                    'name' => $discount['name'],
                    'locality_id' => $localityId,
                ],
                [
                    'description' => $discount['description'],
                    'percentage' => $discount['percentage'],
                    'color' => $discount['color'],
                    'created_by' => $supervisorId,
                    'created_at' => now(),
                ]
            );
        }
    }
}
