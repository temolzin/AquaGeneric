<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class MembershipsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
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
                'duration' => 3,
            ],
            [
                'name' => 'Premium Plan - 6 Months', 
                'price' => 499.00,
                'duration' => 6,
            ],
            [
                'name' => 'Enterprise Plan - 12 Months',
                'price' => 899.00,
                'duration' => 12,
            ]
        ];

        foreach ($memberships as $membership) {
            DB::table('memberships')->insert([
                'name' => $membership['name'],
                'price' => $membership['price'],
                'duration' => $membership['duration'],
                'created_by' => $admin->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
