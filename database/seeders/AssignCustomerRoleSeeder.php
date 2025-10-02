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
            $this->command->error('ERROR: El rol "cliente" no existe. Deteniendo ejecución.');
            return;
        }

        $this->command->info('Rol "cliente" encontrado.');

        $usersWithoutRoles = User::doesntHave('roles')->get();
        
        $this->command->info("Encontrados {$usersWithoutRoles->count()} usuarios sin roles.");

        if ($usersWithoutRoles->count() === 0) {
            $this->command->info('No hay usuarios sin roles. No se realizaron cambios.');
            return;
        }

        $assignedCount = 0;
        foreach ($usersWithoutRoles as $user) {
            if (!$user->hasRole('cliente')) {
                $user->assignRole('cliente'); 
                $assignedCount++;
            }
        }

        $this->command->info("Rol 'cliente' asignado a {$assignedCount} usuarios exitosamente.");
        
        $totalWithClientRole = User::role('cliente')->count(); 
        
        $this->command->info("Total de usuarios con rol 'cliente': {$totalWithClientRole}");
    }
}
