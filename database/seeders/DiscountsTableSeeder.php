<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Discount;
use App\Models\User;

class DiscountsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
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
                'percentage' => 50,
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
                'color' => color(0),
            ],
            [
                'name' => 'Personas con Discapacidad',
                'description' => 'Apoyo para personas con discapacidad.',
                'percentage' => 35,
                'color' => color(10),
            ],
            [
                'name' => 'Convenio Especial',
                'description' => 'Convenio autorizado por el administrador.',
                'percentage' => 25,
                'color' => color(4),
            ],
            [
                'name' => 'Empleado',
                'description' => 'Descuento para empleados.',
                'percentage' => 20,
                'color' => color(1),
            ],
            [
                'name' => 'Programa Social',
                'description' => 'Beneficiarios de programas sociales.',
                'percentage' => 15,
                'color' => color(16),
            ],
            [
                'name' => 'Apoyo Municipal',
                'description' => 'Descuento autorizado por el municipio.',
                'percentage' => 10,
                'color' => color(14),
            ],
        ];

        foreach ($localityIds as $localityId) {
            $userIds = DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->whereIn('roles.name', [User::ROLE_SUPERVISOR, User::ROLE_SECRETARY])
                ->where('users.locality_id', $localityId)
                ->distinct()
                ->pluck('users.id')
                ->toArray();

            if (empty($userIds)) {
                continue;
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
                        'created_by' => collect($userIds)->random(),
                        'created_at' => now(),
                    ]
                );
            }
        }
    }
}
