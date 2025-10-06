<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AssignCustomerRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $clientRole = \Spatie\Permission\Models\Role::where('name', 'cliente')->first();
        
        if (!$clientRole) {
            return;
        }

        $usersWithoutRoles = User::doesntHave('roles')->get();

        foreach ($usersWithoutRoles as $user) {
            if (!$user->hasRole('cliente')) {
                $user->assignRole('cliente'); 
            }
        }
    }
}
