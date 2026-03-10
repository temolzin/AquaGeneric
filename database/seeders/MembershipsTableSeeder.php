<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class MembershipsTableSeeder extends Seeder
{
    public function run()
    {
        // the membership.created_by column is nullable, so the seeder should
        // run even if no administrative user exists yet.  Try to pick an admin
        // first; fall back to the first user or null.
        $adminId = User::whereHas('roles', fn($q) => $q->where('name', 'Admin'))
            ->orderBy('id')
            ->value('id');

        if (is_null($adminId)) {
            $adminId = User::orderBy('id')->value('id');
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

        $now = now();

         $payload = collect($memberships)->map(function ($m) use ($adminId, $now) {
            return [
                'name' => $m['name'],
                'price' => $m['price'],
                'term_months' => $m['term_months'],
                'water_connections_number' => $m['water_connections_number'],
                'users_number' => $m['users_number'],
                'created_by' => $adminId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        })->toArray();

        DB::table('memberships')->upsert(
            $payload,
            ['name'],
            ['price', 'term_months', 'water_connections_number', 'users_number', 'updated_at']
        );

        DB::table('memberships')
            ->where('water_connections_number', 0)
            ->orWhere('users_number', 0)
            ->update([
                'water_connections_number' => 1000,
                'users_number' => 1,
                'updated_at' => now(),
            ]);
    }
}
