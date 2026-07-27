<?php

namespace Database\Seeders;

use App\Models\Discount;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiscountsTableSeeder extends Seeder
{
    public function run(): void
    {
        $localityIds = DB::table('localities')
            ->whereNull('deleted_at')
            ->pluck('id')
            ->toArray();

        if (empty($localityIds)) {
            $this->command->error('No hay localidades activas. Se omitió el seeding de descuentos.');
            return;
        }

        $baseDiscounts = [
            [
                'name' => 'Adulto Mayor',
                'description' => 'Descuento para personas de la tercera edad.',
                'percentage' => 35,
                'color' => color(13),
            ],
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
                'color' => color(6),
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
                ->whereNull('users.deleted_at')
                ->distinct()
                ->pluck('users.id')
                ->toArray();

            if (empty($userIds)) {
                $this->command->warn("La localidad {$localityId} no tiene Supervisor ni Secretaria; se omitieron sus descuentos.");
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
                        'created_by' => $userIds[array_rand($userIds)],
                        'deleted_at' => null,
                    ]
                );
            }
        }
    }
}
