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
        $query = WaterConnection::where('locality_id', $authUser->locality_id)->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $search = $request->input('search');
        
            $query->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                ->orWhere('name', 'LIKE', "%{$search}%")
                ->orWhere('type', $search);
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

        if ($request->has('all_days')) {
            $waterConnectionData['water_days'] = 7;
        }

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
        if ($connection) {
            $connection->name = $request->input('nameUpdate');
            $connection->customer_id = $request->input('customerIdUpdate');
            $connection->type = $request->input('typeUpdate');
            $connection->occupants_number = $request->input('occupantsNumberUpdate');
            $connection->water_days = $request->has('all_days_update') ? 7 : $request->input('waterDaysUpdate');
            $connection->has_water_pressure = $request->input('hasWaterPressureUpdate');
            $connection->has_cistern = $request->input('hasCisternUpdate');
            $connection->cost_id = $request->input('costIdUpdate');

            $connection->save();

            return redirect()->route('waterConnections.index')->with('success', 'Usuario actualizado correctamente.');
        }

        return redirect()->back()->with('error', 'Usuario no encontrado.');
}

    public function destroy($id)
    {
        $connection = WaterConnection::find($id);
        $connection->delete();
        return redirect()->route('waterConnections.index')->with('success', 'Toma de Agua eliminada correctamente.');
    }
}