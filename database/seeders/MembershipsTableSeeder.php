<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class MembershipsTableSeeder extends Seeder
{
    public function run()
    {
        $adminId = User::whereHas('roles', fn($q) => $q->where('name', 'Admin'))
            ->orderBy('id')
            ->value('id');

        if (is_null($adminId)) {
            $adminId = User::orderBy('id')->value('id');
        }

        $memberships = [
            [
                'name' => 'Plan Básico',
                'price' => 299.00,
                'term_months' => 3,
                'water_connections_number' => 1000,
                'users_number' => 1,
            ],
            [
                'name' => 'Plan Premium',
                'price' => 499.00,
                'term_months' => 6,
                'water_connections_number' => 4000,
                'users_number' => 3,
            ],
            [
                'name' => 'Plan Empresarial',
                'price' => 899.00,
                'term_months' => 12,
                'water_connections_number' => 10000,
                'users_number' => 5,
            ]
        ];

        $now = now();

        foreach ($memberships as $membership) {
            $data = [
                'name' => $membership['name'],
                'price' => $membership['price'],
                'term_months' => $membership['term_months'],
                'water_connections_number' => $membership['water_connections_number'],
                'users_number' => $membership['users_number'],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            Schema::hasColumn('memberships', 'created_by') && $adminId && $data['created_by'] = $adminId;

            DB::table('memberships')->updateOrInsert(
                ['term_months' => $membership['term_months']],
                $data
            );
        }
    }
}
