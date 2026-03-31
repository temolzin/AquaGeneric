<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\DebtCategory;

class DebtCategoriesTableSeeder extends Seeder
{
    public function run()
    {
        // Ensure global Servicio de Agua exists
        $service = DB::table('debt_categories')->where('name', DebtCategory::NAME_SERVICE)->first();
        if (! $service) {
            DB::table('debt_categories')->insert([
                'name' => DebtCategory::NAME_SERVICE,
                'description' => 'Categoría global para Servicio de Agua',
                'color' => '#007bff',
                'locality_id' => null,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Optional locality-specific categories
        $localityIds = DB::table('localities')->pluck('id');
        $localCategories = ['Mantenimiento', 'Multa', 'Recargo'];

        foreach ($localityIds as $localityId) {
            foreach ($localCategories as $name) {
                $exists = DB::table('debt_categories')
                    ->where('name', $name)
                    ->where('locality_id', $localityId)
                    ->exists();

                if (! $exists) {
                    DB::table('debt_categories')->insert([
                        'name' => $name,
                        'description' => null,
                        'color' => null,
                        'locality_id' => $localityId,
                        'created_by' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
