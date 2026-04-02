<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\DebtCategory;
use App\Models\User;

class DebtCategoriesTableSeeder extends Seeder
{
    public function run()
    {
        $defaultUser = User::first()?->id ?? 1;
        DB::table('debt_categories')->updateOrInsert(
            [
                'name' => DebtCategory::NAME_SERVICE,
                'locality_id' => null
            ],
            [
                'description' => 'Cobro por consumo de agua',
                'color' => 'bg-primary',
                'created_by' => $defaultUser,
            ]
        );

        $localityIds = DB::table('localities')->pluck('id');
        $usersByLocality = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->whereIn('roles.name', [User::ROLE_SUPERVISOR, User::ROLE_SECRETARY])
            ->select('users.id', 'users.locality_id')
            ->get()
            ->groupBy('locality_id');

        $baseCategories = [
            [
                'name' => 'Mantenimiento',
                'description' => 'Costos por mantenimiento del sistema',
                'color' => 'bg-success',
            ],
            [
                'name' => 'Multa',
                'description' => 'Penalización por incumplimiento',
                'color' => 'bg-danger',
            ],
            [
                'name' => 'Recargo',
                'description' => 'Cargo adicional por retraso en pago',
                'color' => 'bg-warning',
            ],
        ];

        $data = [];

        foreach ($localityIds as $localityId) {
            $createdBy = $usersByLocality[$localityId]->first()->id ?? $defaultUser;

            foreach ($baseCategories as $category) {
                $data[] = [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'color' => $category['color'],
                    'locality_id' => $localityId,
                    'created_by' => $createdBy,
                ];
            }
        }

        DB::table('debt_categories')->upsert(
            $data,
            ['name', 'locality_id'],
            []
        );
    }
}
