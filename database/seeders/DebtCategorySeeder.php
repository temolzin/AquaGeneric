<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DebtCategory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DebtCategorySeeder extends Seeder
{
    public function run()
    {
        $categoryConfigs = [
            [
                'name' => 'Servicio de Agua',
                'description' => 'Pago correspondiente al suministro de agua.',
                'color' => 'bg-primary',
            ],
            [
                'name' => 'Mantenimiento',
                'description' => 'Cuotas para mantenimiento general.',
                'color' => 'bg-warning',
            ],
            [
                'name' => 'Multas',
                'description' => 'Sanciones por incumplimientos.',
                'color' => 'bg-danger',
            ],
        ];

        $createdBy = $this->getDefaultUserId();

        // 🔥 Obtener todas las localidades
        $localities = DB::table('localities')->pluck('id');

        foreach ($localities as $localityId) {
            foreach ($categoryConfigs as $categoryConfig) {

                DebtCategory::updateOrCreate(
                    [
                        'name' => $categoryConfig['name'],
                        'locality_id' => $localityId,
                    ],
                    [
                        'description' => $categoryConfig['description'],
                        'color' => $categoryConfig['color'],
                        'created_by' => $createdBy,
                    ]
                );
            }
        }
    }

    private function getDefaultUserId(): ?int
    {
        return DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->whereIn('roles.name', [
                User::ROLE_SUPERVISOR,
                User::ROLE_SECRETARY,
            ])
            ->distinct()
            ->value('users.id');
    }
}
