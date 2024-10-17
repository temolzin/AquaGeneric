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
        $roleSecretariat = Role::create(['name' => 'Secretaria']);
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
            'description' => 'Permite ver los Clientes.'
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
            'description' => 'Permite eliminar los Costos.'
        ])->assignRole([$roleSupervisor]);
        Permission::create([
            'name' => 'editCost',
            'description' => 'Permite editar los Costos.'
        ])->assignRole([$roleSupervisor]);
        Permission::create([
            'name' => 'deletePayment',
            'description' => 'Permite eliminar los Pagos.'
        ])->assignRole([$roleSupervisor]);
        Permission::create([
            'name' => 'editPayment',
            'description' => 'Permite editar los Pagos.'
        ])->assignRole([$roleSupervisor]);
        Permission::create([
            'name' => 'deleteDebt',
            'description' => 'Permite eliminar Deuda.'
        ])->assignRole([$roleSupervisor]);
        Permission::create([
            'name' => 'editCustomer',
            'description' => 'Permite editar los Clientes.'
        ])->assignRole([$roleSupervisor]);
        Permission::create([
            'name' => 'deleteCustomer',
            'description' => 'Permite eliminar los Clientes.'
        ])->assignRole([$roleSupervisor]);
        Permission::create([
            'name' => 'viewLocality',
            'description' => 'Permite ver las localidades.'
        ])->assignRole([$roleAdmin]);
        Permission::create([
            'name' => 'editLocality',
            'description' => 'Permite editar las localidades.'
        ])->assignRole([$roleAdmin]);
        Permission::create([
            'name' => 'deleteLocality',
            'description' => 'Permite eliminar localidades.'
        ])->assignRole([$roleAdmin]);
        Permission::create([
            'name' => 'selectLocality',
            'description' => 'Permite seleccionar Localidades.'
        ])->assignRole([$roleSecretariat, $roleSupervisor]);        
        Permission::create([
            'name' => 'viewDashboardCards',
            'description' => 'Permite ver las tarjetas de información en el dashboard.'
        ])->assignRole([$roleSupervisor, $roleSecretariat]);
        Permission::create([
            'name' => 'viewLocalityCharts',
            'description' => 'Permite ver las gráficas correspondientes a una localidad en el dashboard.'
        ])->assignRole($roleAdmin);
    }
}
