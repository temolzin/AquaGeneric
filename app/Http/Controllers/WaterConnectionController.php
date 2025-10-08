<?php

namespace App\Http\Controllers;

use App\Models\WaterConnection;
use App\Models\Customer;
use App\Models\Cost;
use Illuminate\Http\Request;

class WaterConnectionController extends Controller
{
    public function index(Request $request)
    {
        $authUser = auth()->user();

        $query = WaterConnection::withoutGlobalScope(WaterConnection::SCOPE_NOT_CANCELED)
            ->where('water_connections.locality_id', $authUser->locality_id)
            ->join('customers', 'water_connections.customer_id', '=', 'customers.id')
            ->orderBy('water_connections.created_at', 'desc')
            ->select('water_connections.*');

        if ($request->has('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('water_connections.id', 'LIKE', "%{$search}%")
                ->orWhere('water_connections.name', 'LIKE', "%{$search}%")
                ->orWhere('water_connections.type', $search)
                ->orWhere('customers.name', 'LIKE', "%{$search}%")
                ->orWhere('customers.last_name', 'LIKE', "%{$search}%");
            });
        }

        $connections = $query->paginate(10);
        $customers = Customer::where('locality_id', $authUser->locality_id)->get();
        $costs = Cost::where('locality_id', $authUser->locality_id)->get();

        return view('waterConnections.index', compact('connections', 'customers', 'costs'));
    }

    public function store(Request $request)
    {
        $authUser = auth()->user();

        $waterConnectionData = $request->all();

        $waterConnectionData['water_days'] = json_encode(
            $request->has('all_days') ? 'all' : $request->input('days', [])
        );

        $waterConnectionData['locality_id'] = $authUser->locality_id;
        $waterConnectionData['created_by'] = $authUser->id;

        WaterConnection::create($waterConnectionData);

        return redirect()->route('waterConnections.index')->with('success', 'Toma de agua registrada correctamente.');
    }

    public function show($id)
    {
        $connections = WaterConnection::findOrFail($id);
        return view('waterConnections.show', compact('connections'));
    }

    public function update(Request $request, $id)
    {
        $connection = WaterConnection::find($id);

        if (!$connection) {
            return redirect()->back()->with('error', 'Toma de Agua no encontrada.');
        }

        $connection->name = $request->input('nameUpdate');
        $connection->customer_id = $request->input('customerIdUpdate');
        $connection->type = $request->input('typeUpdate');
        $connection->occupants_number = $request->input('occupantsNumberUpdate');

        $connection->water_days = json_encode(
            $request->has('all_days_update') 
                ? 'all' 
                : $request->input('days_update', [])
        );

        $connection->street = $request->input('streetUpdate');
        $connection->block = $request->input('blockUpdate');
        $connection->exterior_number = $request->input('exteriorNumberUpdate');
        $connection->interior_number = $request->input('interiorNumberUpdate');
        $connection->has_water_pressure = $request->input('hasWaterPressureUpdate');
        $connection->has_cistern = $request->input('hasCisternUpdate');
        $connection->cost_id = $request->input('costIdUpdate');
        $connection->note = $request->input('noteUpdate');

        $connection->save();

        return redirect()->route('waterConnections.index')->with('success', 'Toma de Agua actualizada correctamente.');
    }

    public function destroy($id)
    {
        $connection = WaterConnection::find($id);
        $connection->delete();
        return redirect()->route('waterConnections.index')->with('success', 'Toma de Agua eliminada correctamente.');
    }

    public function cancel(Request $request, $id)
    {
        $connection = WaterConnection::findOrFail($id);

        if ($connection->hasDebt()) {
            return redirect()->route('waterConnections.index')
                ->with('debtError', true)
                ->with('connectionName', $connection->name);
        }

        $connection->cancel_description = $request->input('cancelDescription');
        $connection->canceled_at = now();
        $connection->is_canceled = true;
        $connection->save();

        return redirect()->route('waterConnections.index')->with('success', 'Toma cancelada correctamente.');
    }

    public function reactivate(Request $request, $id)
    {
        $connection = WaterConnection::withoutGlobalScope(WaterConnection::SCOPE_NOT_CANCELED)->findOrFail($id);

        if ($request->has('customer_id')) {
            $connection->customer_id = $request->input('customer_id');
        }

        $connection->is_canceled = false;
        $connection->canceled_at = null;
        $connection->cancel_description = null;
        $connection->save();

        return redirect()->route('waterConnections.index')->with('success', 'Toma reactivada y asignada correctamente.');
    }

    public function showCustomerWaterConnections()
    {
        $authUser = auth()->user();
        $customer = \App\Models\Customer::where('user_id', $authUser->id)->first();
        
        if (!$customer) {
            $connections = collect();
        } else {
            $query = WaterConnection::with(['cost', 'locality'])
                ->where('customer_id', $customer->id)
                ->where('locality_id', $authUser->locality_id);
            
            if (request()->has('search') && request('search') != '') {
                $search = request('search');
                $query->where(function($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('street', 'like', "%{$search}%")
                    ->orWhere('block', 'like', "%{$search}%");
                });
            }
            
            $connections = $query->paginate(10)->appends(request()->query());
            $connections->getCollection()->transform(function ($connection) {
                $connection->formatted_water_days = $this->getFormattedWaterDays($connection->water_days);
                $connection->full_address = $this->getFullAddress($connection);
                $connection->water_pressure_text = $connection->has_water_pressure ? 'Sí' : 'No';
                $connection->cistern_text = $connection->has_cistern ? 'Sí' : 'No';
                
                return $connection;
            });
        }

        return view('viewCustomerWaterConnections.index', compact('connections'));
    }

    private function getFormattedWaterDays($waterDays)
    {
        if (empty($waterDays) || $waterDays === 'null' || $waterDays === '[]') {
            return [
                'days_array' => [],
                'formatted_text' => 'No hay días específicos asignados',
                'has_days' => false
            ];
        }
        
        if ($waterDays === '"all"' || $waterDays === 'all') {
            return $this->getAllDaysFormatted();
        }
        
        $waterDaysArray = json_decode($waterDays, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $waterDaysArray = [$waterDays];
        }
        
        if (is_array($waterDaysArray) && count($waterDaysArray) === 1 && is_string($waterDaysArray[0])) {
            $firstItem = $waterDaysArray[0];
            
            if ($firstItem === 'all') {
                return $this->getAllDaysFormatted();
            }
            
            $waterDaysArray = [$firstItem];
        }
        
        $daysOfWeek = [
            'monday' => 'Lunes',
            'tuesday' => 'Martes',
            'wednesday' => 'Miércoles',
            'thursday' => 'Jueves',
            'friday' => 'Viernes',
            'saturday' => 'Sábado',
            'sunday' => 'Domingo'
        ];
        
        $dayVariations = [
            'monday' => ['monday', 'lunes', 'mon', 'lun'],
            'tuesday' => ['tuesday', 'martes', 'tue', 'mar'],
            'wednesday' => ['wednesday', 'miércoles', 'miercoles', 'wed', 'mie'],
            'thursday' => ['thursday', 'jueves', 'thu', 'jue'],
            'friday' => ['friday', 'viernes', 'fri', 'vie'],
            'saturday' => ['saturday', 'sábado', 'sabado', 'sat', 'sab'],
            'sunday' => ['sunday', 'domingo', 'sun', 'dom']
        ];
        
        $activeDays = [];
        $allDays = [];
        
        foreach ($daysOfWeek as $key => $day) {
            $isActive = false;
            
            if (is_array($waterDaysArray)) {
                foreach ($waterDaysArray as $waterDay) {
                    $waterDayLower = strtolower(trim($waterDay));
                    
                    if (in_array($waterDayLower, $dayVariations[$key])) {
                        $isActive = true;
                        break;
                    }
                    
                    if ($waterDayLower === 'all') {
                        $isActive = true;
                        break;
                    }
                }
            }
            
            $allDays[$key] = [
                'name' => $day,
                'active' => $isActive
            ];
            
            if ($isActive) {
                $activeDays[] = $day;
            }
        }
        
        return [
            'days_array' => $allDays,
            'active_days' => $activeDays,
            'formatted_text' => count($activeDays) > 0 ?
                (count($activeDays) === 7 ? 'Todos los días' : implode(', ', $activeDays)) :
                'No hay días activos',
            'has_days' => count($activeDays) > 0
        ];
    }

    private function getAllDaysFormatted()
    {
        $daysOfWeek = [
            'monday' => 'Lunes',
            'tuesday' => 'Martes',
            'wednesday' => 'Miércoles',
            'thursday' => 'Jueves',
            'friday' => 'Viernes',
            'saturday' => 'Sábado',
            'sunday' => 'Domingo'
        ];
        
        $allDays = [];
        foreach ($daysOfWeek as $key => $day) {
            $allDays[$key] = [
                'name' => $day,
                'active' => true
            ];
        }
        
        return [
            'days_array' => $allDays,
            'active_days' => array_values($daysOfWeek),
            'formatted_text' => 'Todos los días',
            'has_days' => true
        ];
    }

    private function getFullAddress($connection)
    {
        $addressParts = [];
        
        if ($connection->block) {
            $addressParts[] = "Manzana {$connection->block}";
        }
        
        if ($connection->street) {
            $addressParts[] = "Calle {$connection->street}";
        }
        
        if ($connection->exterior_number) {
            $addressParts[] = "#{$connection->exterior_number}";
        }
        
        if ($connection->interior_number) {
            $addressParts[] = "Int. {$connection->interior_number}";
        }
        
        if (empty($addressParts)) {
            return [
                'full_text' => 'Dirección no especificada',
                'has_address' => false
            ];
        }
        
        return [
            'full_text' => implode(', ', $addressParts),
            'has_address' => true,
            'parts' => $addressParts
        ];
    }
}
