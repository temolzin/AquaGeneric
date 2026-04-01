<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\DebtCategory;

class DebtCategoriesTableSeeder extends Seeder
{
    public function run()
    {
        DebtCategory::firstOrCreate(
            [
                'name' => DebtCategory::NAME_SERVICE,
                'locality_id' => null
            ],
            [
                'description' => 'Categoría global para Servicio de Agua',
                'color' => 'bg-primary',
                'created_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $localities = DB::table('localities')->get();
        foreach ($localities as $locality) {
            DebtCategory::firstOrCreate(
                [
                    'name' => 'Mantenimiento',
                    'locality_id' => $locality->id
                ],
                [
                    'description' => 'Categoría Mantenimiento',
                    'color' => 'bg-success',
                    'created_by' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            DebtCategory::firstOrCreate(
                [
                    'name' => 'Multa',
                    'locality_id' => $locality->id
                ],
                [
                    'description' => 'Categoría Multa',
                    'color' => 'bg-danger',
                    'created_by' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            DebtCategory::firstOrCreate(
                [
                    'name' => 'Recargo',
                    'locality_id' => $locality->id
                ],
                [
                    'description' => 'Categoría Recargo',
                    'color' => 'bg-warning',
                    'created_by' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
