<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Membership;
use App\Models\User;
use Spatie\Permission\Models\Role;

class MembershipSeeder extends Seeder
{
    public function run(): void
    {
        // Crear o buscar al usuario admin
        $adminUser = User::firstOrCreate(
            ['email' => 'jose@gmail.com'],
            [
                'locality_id' => null,
                'name' => 'Jose',
                'last_name' => 'Lopez Osorio',
                'phone' => '7798745677',
                'password' => Hash::make('12345'),
            ]
        );
        if (Role::where('name', 'Admin')->exists()) {
            $adminUser->assignRole('Admin');
            $this->command->info('Admin role assigned to Jose');
        }

        $membershipPlans = [
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
            ],
        ];

        $createdCount = 0;

        foreach ($membershipPlans as $plan) {
            Membership::create([
                'created_by' => $adminUser->id,
                'name' => $plan['name'],
                'price' => $plan['price'],
                'duration' => $plan['duration'],
            ]);

        }
    }
}
