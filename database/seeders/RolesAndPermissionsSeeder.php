<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        $roleAdmin = Role::create(['name' => 'Admin']);
        $roleSecretariat = Role::create(['name' => 'secretariat']);
        $roleSupervisor = Role::create(['name' => 'Supervisor']);
      
        Permission::create([
            'name' => 'viewUser',
            'description' => 'Permite ver los Usuario.'
        ])->assignRole($roleAdmin);
        Permission::create([
            'name' => 'viewRoles',
            'description' => 'Permite ver los Roles.'
        ])->assignRole($roleAdmin);
        Permission::create([
            'name' => 'viewCustomers',
            'description' => 'Permite ver los Usuarios.'
        ])->assignRole([$roleSupervisor, $roleSecretariat ]);
        Permission::create([
            'name' => 'viewPayments',
            'description' => 'Permite ver los Pagos.'
        ])->assignRole([$roleSupervisor, $roleSecretariat ]);
        Permission::create([
            'name' => 'viewDebts',
            'description' => 'Permite ver las Deudas.'
        ])->assignRole([$roleSupervisor, $roleSecretariat ]);
        Permission::create([
            'name' => 'viewCost',
            'description' => 'Permite ver los Costos.'
        ])->assignRole([$roleSupervisor, $roleSecretariat ]);
        Permission::create([
            'name' => 'deleteCost',
            'description' => 'Permite eliminar los costos.'
        ])->assignRole([$roleSupervisor]);
        Permission::create([
            'name' => 'editCost',
            'description' => 'Permite editar los costos.'
        ])->assignRole([$roleSupervisor]);
        Permission::create([
            'name' => 'deletePayment',
            'description' => 'Permite editar los pagos.'
        ])->assignRole([$roleSupervisor]);
        Permission::create([
            'name' => 'editPayment',
            'description' => 'Permite editar los pagos.'
        ])->assignRole([$roleSupervisor]);
        Permission::create([
            'name' => 'deleteDebt',
            'description' => 'Permite eliminar deuda.'
        ])->assignRole([$roleSupervisor]);
        Permission::create([
            'name' => 'editDebts',
            'description' => 'Permite ver los Costos.'
        ])->assignRole([$roleSupervisor]);
        Permission::create([
            'name' => 'editCustomer',
            'description' => 'Permite ver los Costos.'
        ])->assignRole([$roleSupervisor]);
        Permission::create([
            'name' => 'deleteCustomer',
            'description' => 'Permite ver los Costos.'
        ])->assignRole([$roleSupervisor]);
        Permission::create([
            'name' => 'viewLocation',
            'description' => 'Permite ver las localidades.'
        ])->assignRole([$roleAdmin]);
        Permission::create([
            'name' => 'editLocation',
            'description' => 'Permite editar las localidades.'
        ])->assignRole([$roleAdmin]);
        Permission::create([
            'name' => 'deleteLocation',
            'description' => 'Permite eliminar localidades.'
        ])->assignRole([$roleAdmin]);
        Permission::create([
            'name' => 'selectLocality',
            'description' => 'Permite seleccionar localidades.'
        ])->assignRole([$roleSecretariat, $roleSupervisor]);
    }
}
