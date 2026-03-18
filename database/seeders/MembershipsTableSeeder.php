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
            ->value('id')
            ?? User::orderBy('id')->value('id');

        $memberships = [
            [
                'name' => 'Basic Plan - 3 Months',
                'price' => 299.00,
                'term_months' => 3,
                'water_connections_number' => 1000,
                'users_number' => 1,
            ],
            [
                'name' => 'Premium Plan - 6 Months',
                'price' => 499.00,
                'term_months' => 6,
                'water_connections_number' => 4000,
                'users_number' => 3,
            ],
            [
                'name' => 'Enterprise Plan - 12 Months',
                'price' => 899.00,
                'term_months' => 12,
                'water_connections_number' => 10000,
                'users_number' => 5,
            ]
        ];

        $now = now();

        $payload = collect($memberships)->map(function ($m) use ($adminId, $now) {
            $data = [
                'name' => $m['name'],
                'price' => $m['price'],
                'term_months' => $m['term_months'],
                'water_connections_number' => $m['water_connections_number'],
                'users_number' => $m['users_number'],
                'created_at' => $now,
            ];
            
            // Solo agregar created_by si la columna existe
            if (Schema::hasColumn('memberships', 'created_by') && $adminId) {
                $data['created_by'] = $adminId;
            }
            
            return $data;
        })->toArray();

        DB::table('memberships')->upsert(
            $payload,
            ['name'],
            ['price', 'term_months', 'water_connections_number', 'users_number', 'created_by']
        );
    }
}
