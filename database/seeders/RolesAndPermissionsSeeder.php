<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        $roleAdmin = Role::firstOrCreate(['name' => 'Admin']);
        $roleSecretariat = Role::firstOrCreate(['name' => 'Secretaria']);
        $roleSupervisor = Role::firstOrCreate(['name' => 'Supervisor']);
        $roleCliente = Role::firstOrCreate(['name' => 'Cliente']);
      
        Permission::firstOrCreate([
            'name' => 'viewUser',
            'description' => 'Permite ver los Usuario.'
        ])->assignRole($roleAdmin);
        Permission::firstOrCreate([
            'name' => 'viewRoles',
            'description' => 'Permite ver los Roles.'
        ])->assignRole($roleAdmin);
        Permission::firstOrCreate([
            'name' => 'viewCustomers',
            'description' => 'Permite ver los Clientes.'
        ])->assignRole([$roleSupervisor, $roleSecretariat ]);
        Permission::firstOrCreate([
            'name' => 'viewPayments',
            'description' => 'Permite ver los Pagos.'
        ])->assignRole([$roleSupervisor, $roleSecretariat ]);
        Permission::firstOrCreate([
            'name' => 'viewDebts',
            'description' => 'Permite ver las Deudas.'
        ])->assignRole([$roleSupervisor, $roleSecretariat ]);
        Permission::firstOrCreate([
            'name' => 'viewCost',
            'description' => 'Permite ver los Costos.'
        ])->assignRole([$roleSupervisor, $roleSecretariat ]);
        Permission::firstOrCreate([
            'name' => 'viewWaterConnection',
            'description' => 'Permite ver las Tomas de Agua.'
        ])->assignRole([$roleSupervisor, $roleSecretariat ]);
        Permission::firstOrCreate([
            'name' => 'deleteWaterConnection',
            'description' => 'Permite eliminar las Tomas de Agua.'
        ])->assignRole([$roleSupervisor]);
        Permission::firstOrCreate([
            'name' => 'editWaterConnection',
            'description' => 'Permite editar las Tomas de Agua.'
        ])->assignRole([$roleSupervisor]);
        Permission::firstOrCreate([
            'name' => 'deleteCost',
            'description' => 'Permite eliminar los Costos.'
        ])->assignRole([$roleSupervisor]);
        Permission::firstOrCreate([
            'name' => 'editCost',
            'description' => 'Permite editar los Costos.'
        ])->assignRole([$roleSupervisor]);
        Permission::firstOrCreate([
            'name' => 'deletePayment',
            'description' => 'Permite eliminar los Pagos.'
        ])->assignRole([$roleSupervisor]);
        Permission::firstOrCreate([
            'name' => 'editPayment',
            'description' => 'Permite editar los Pagos.'
        ])->assignRole([$roleSupervisor]);
        Permission::firstOrCreate([
            'name' => 'deleteDebt',
            'description' => 'Permite eliminar Deuda.'
        ])->assignRole([$roleSupervisor]);
        Permission::firstOrCreate([
            'name' => 'editCustomer',
            'description' => 'Permite editar los Clientes.'
        ])->assignRole([$roleSupervisor]);
        Permission::firstOrCreate([
            'name' => 'deleteCustomer',
            'description' => 'Permite eliminar los Clientes.'
        ])->assignRole([$roleSupervisor]);
        Permission::firstOrCreate([
            'name' => 'viewLocality',
            'description' => 'Permite ver las localidades.'
        ])->assignRole([$roleAdmin]);
        Permission::firstOrCreate([
            'name' => 'editLocality',
            'description' => 'Permite editar las localidades.'
        ])->assignRole([$roleAdmin]);
        Permission::firstOrCreate([
            'name' => 'deleteLocality',
            'description' => 'Permite eliminar localidades.'
        ])->assignRole([$roleAdmin]);
        Permission::firstOrCreate([
            'name' => 'selectLocality',
            'description' => 'Permite seleccionar Localidades.'
        ])->assignRole([$roleSecretariat, $roleSupervisor]);        
        Permission::firstOrCreate([
            'name' => 'viewDashboardCards',
            'description' => 'Permite ver las tarjetas de información en el dashboard.'
        ])->assignRole([$roleSupervisor, $roleSecretariat]);
        Permission::firstOrCreate([
            'name' => 'viewLocalityCharts',
            'description' => 'Permite ver las gráficas correspondientes a una localidad en el dashboard.'
        ])->assignRole($roleAdmin);
        Permission::firstOrCreate([
            'name' => 'viewGeneralExpense',
            'description' => 'Permite ver los Gastos.'
        ])->assignRole([$roleSupervisor, $roleSecretariat ]);
        Permission::firstOrCreate([
            'name' => 'deleteGeneralExpense',
            'description' => 'Permite eliminar los Gastos.'
        ])->assignRole([$roleSupervisor]);
        Permission::firstOrCreate([
            'name' => 'editGeneralExpense',
            'description' => 'Permite editar los Gastos.'
        ])->assignRole([$roleSupervisor]);
        Permission::firstOrCreate([
            'name' => 'viewIncidentCategories',
            'description' => 'Permite ver las Categorías de Incidentes.'
        ])->assignRole([$roleSupervisor, $roleSecretariat]);
        Permission::firstOrCreate([
            'name' => 'editIncidentCategories',
            'description' => 'Permite editar Categorías de Incidentes.'
        ])->assignRole($roleSupervisor);
        Permission::firstOrCreate([
            'name' => 'deleteIncidentCategories',
            'description' => 'Permite eliminar Categorías de Incidentes.'
        ])->assignRole($roleSupervisor);
        Permission::firstOrCreate([
            'name' => 'viewAdvancePayments',
            'description' => 'Permite ver los Pagos Anticipados.'
        ])->assignRole([$roleSupervisor, $roleSecretariat]);
        Permission::firstOrCreate([
            'name' => 'viewIncidents',
            'description' => 'Permite ver los Incidentes.'
        ])->assignRole([$roleSupervisor, $roleSecretariat]);
        Permission::firstOrCreate([
            'name' => 'editIncidents',
            'description' => 'Permite editar Incidentes.'
        ])->assignRole($roleSupervisor);
        Permission::firstOrCreate([
            'name' => 'deleteIncidents',
            'description' => 'Permite eliminar Incidentes.'
        ])->assignRole($roleSupervisor);
              Permission::firstOrCreate([
            'name' => 'viewEmployee',
            'description' => 'Permite ver a los Empleados.'
        ])->assignRole([$roleSupervisor, $roleSecretariat]);
        Permission::firstOrCreate([
            'name' => 'editEmployee',
            'description' => 'Permite editar a los Empleados.'
        ])->assignRole([$roleSupervisor]);
        Permission::firstOrCreate([
            'name' => 'deleteEmployee',
            'description' => 'Permite eliminar a los Empleados.'
        ])->assignRole([$roleSupervisor]);
        Permission::firstOrCreate([
            'name' => 'viewIncidentStatuses',
            'description' => 'Permite ver los estatus de una incidencia.'
        ])->assignRole([$roleSupervisor, $roleSecretariat]);
        Permission::firstOrCreate([
            'name' => 'editIncidentStatuses',
            'description' => 'Permite editar el estatus de una incidencia.'
        ])->assignRole([$roleSupervisor]);
        Permission::firstOrCreate([
            'name' => 'deleteIncidentStatuses',
            'description' => 'Permite eliminar los estatus de una incidencia.'
        ])->assignRole([$roleSupervisor]);
        Permission::firstOrCreate([
            'name' => 'viewCustomerPayments',
            'description' => 'El cliente puede ver sus pagos'
        ])->assignRole([$roleCliente]);
         Permission::firstOrCreate([
            'name' => 'viewCustomerDebts',
            'description' => 'El cliente puede ver sus deudas'
        ])->assignRole([$roleCliente]);
        Permission::firstOrCreate([
            'name' => 'viewWaterConnections',
            'description' => 'El cliente puede ver sus tomas de agua'
        ])->assignRole([$roleCliente]);
        Permission::firstOrCreate([
            'name' => 'viewInventory',
            'description' => 'Permite ver el Inventario.'
        ])->assignRole([$roleSupervisor]);
        Permission::firstOrCreate([
            'name' => 'updateInventory',
            'description' => 'Permite editar el Inventario.'
        ])->assignRole([$roleSupervisor]);
        Permission::firstOrCreate([
            'name' => 'deleteInventory',
            'description' => 'Permite eliminar el Inventario.'
        ])->assignRole([$roleSupervisor]);
        Permission::firstOrCreate([
            'name' => 'viewMemberships',
            'description' => 'Permite ver las Membresías.'
        ])->assignRole([$roleSupervisor, $roleSecretariat]);
        Permission::firstOrCreate([
            'name' => 'createMemberships',
            'description' => 'Permite crear Membresías.'
        ])->assignRole([$roleSupervisor]);
        Permission::firstOrCreate([
            'name' => 'editMemberships',
            'description' => 'Permite editar las Membresías.'
        ])->assignRole([$roleSupervisor]);
        Permission::firstOrCreate([
            'name' => 'deleteMemberships',
            'description' => 'Permite eliminar las Membresías.'
        ])->assignRole([$roleSupervisor]);
    }
}
