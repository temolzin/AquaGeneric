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
    public function run($localityId = null)
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
        ];

        if ($localityId) {
            $this->seedLocalityDiscounts((int) $localityId, $baseDiscounts);
            return;
        }

        $localityIds = DB::table('localities')->pluck('id')->toArray();

        if (empty($localityIds)) {
            $this->command->info('Default discounts seeded. No localities found for locality discounts.');
            return;
        }

        foreach ($localityIds as $localityId) {
            $this->seedLocalityDiscounts((int) $localityId, $baseDiscounts);
        }
    }

    private function seedLocalityDiscounts(int $localityId, array $baseDiscounts): void
    {
        $userIds = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->whereIn('roles.name', [User::ROLE_SUPERVISOR, User::ROLE_SECRETARY])
            ->where('users.locality_id', $localityId)
            ->distinct()
            ->pluck('users.id')
            ->toArray();

        if (empty($userIds)) {
            $fallbackUserId = DB::table('users')
                ->where('locality_id', $localityId)
                ->value('id');

            if (!$fallbackUserId) {
                return;
            }

            $userIds = [$fallbackUserId];
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
