<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogIncident;

class LogIncidentController extends Controller
{
    public function index()
    {

    }

    public function store(Request $request)
    {
        $authUser = auth()->user();

        $logInventoryData = [
            'locality_id' => $authUser->locality_id,
            'created_by' => $authUser->id,
            'amount' => $request->input('amount'),
            'description' => $request->input('description'),
            'inventory_id' => $request->inventoryId,
        ];

        $logincident = LogIncident::create($logInventoryData);

        return redirect()->route('incidents.index')->with('success', 'Cambio de Estatus de Incidencia Exitoso');
    }
    
}
