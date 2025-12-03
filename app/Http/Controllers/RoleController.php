<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    private function getModuleNames()
    {
        return [
            'User' => 'Usuarios',
            'Role' => 'Roles',
            'Customer' => 'Clientes',
            'Payment' => 'Pagos',
            'Debt' => 'Deudas',
            'Cost' => 'Costos',
            'Waterconnection' => 'Tomas de Agua',
            'Locality' => 'Localidad',
            'Selectlocality' => 'Seleccionar localidad',
            'Dashboardcard' => 'Menú',
            'Localitychart' => 'Gráficas',
            'Generalexpense' => 'Gastos Generales',
            'Incidentcategorie' => 'Categoría de Incidencias',
            'Advancepayment' => 'Pagos Anticipados',
            'Incident' => 'Incidentes',
            'Employee' => 'Empleados',
            'Incidentstatuse' => 'Estatus de Incidencias',
            'Faultreport' => 'Reporte de fallas',
            'Notice' => 'Avisos de Localidades',
            'Customerpayment' => 'Pagos del cliente',
            'Customerdebt' => 'Deudas del cliente',
            'Customernotice' => 'Avisos en el usuario de Cliente',
            'Customerfaultreport' => 'Reporte de fallas en el usuario de Cliente',
            'Inventory' => 'Inventario',
            'Updateinventory' => 'Editar Inventario',
            'Membership' => 'Membresías',
            'Reportslist' => 'Lista de reportes',
            'Expensetype' => 'Tipos de gastos',
            'Section' => 'Secciones',
            'Inventorycategorie' => 'Categoría de Inventario',
        ];
    }

    private function groupPermissions()
    {
        return Permission::all()
            ->groupBy(function ($permission) {
                $module = preg_replace('/^(view|create|edit|delete)/i', '', $permission->name);
                $module = ucfirst(strtolower(rtrim($module, 's')));
                return $module;
            })
            ->map(function ($group) {
                $order = ['view', 'create', 'edit', 'delete'];
                return $group->sortBy(function ($perm) use ($order) {
                    foreach ($order as $i => $prefix) {
                        if (stripos($perm->name, $prefix) === 0) {
                            return $i;
                        }
                    }
                    return count($order);
                });
            });
    }

    public function index()
    {
        $permissions = $this->groupPermissions();
        $moduleNames = $this->getModuleNames();
        $roles = Role::orderBy('created_at', 'desc')->get();

        return view('roles.index', compact('roles', 'permissions', 'moduleNames'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        if (Role::where('name', $request->name)->exists()) {
            return redirect()->route('roles.index')
                ->with('error', 'El rol ya existe.');
        }

        $role = Role::create(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', 'Rol creado correctamente');
    }

    public function edit(Role $role)
    {
        $permissions = $this->groupPermissions();
        $moduleNames = $this->getModuleNames();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions', 'moduleNames'));
    }

    public function update(Request $request, $id)
    {
        $existingRole = Role::where('name', $request->name)->where('id', '!=', $id)->first();
        if ($existingRole) {
            return redirect()->back()->with('error', 'El rol ya existe.');
        }

        $role = Role::findOrFail($id);
        $role->update(['name' => $request->name]);

        $role->permissions()->sync($request->permissions ?? []);

        return redirect()->route('roles.index')->with('success', 'Rol actualizado correctamente');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Rol eliminado exitosamente');
    }
}
