<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class MembershipsTableSeeder extends Seeder
{
    public function run()
    {
        $admin = User::whereHas('roles', function($q) {
            $q->where('name', 'Admin');
        })->first();

        if (!$admin) {
            $admin = User::first();
        }

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

        foreach ($memberships as $membership) {
            DB::table('memberships')->insert([
                'name' => $membership['name'],
                'price' => $membership['price'],
                'term_months' => $membership['term_months'],
                'water_connections_number' => $membership['water_connections_number'],
                'users_number' => $membership['users_number'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
