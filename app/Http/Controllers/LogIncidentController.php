<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogIncident;
use App\Models\Employee;

class LogIncidentController extends Controller
{
    public function index()
    {

    }

    public function store(Request $request)
    {
        $authUser = auth()->user();

        $logIncidentData = [
            'locality_id' => $authUser->locality_id,
            'created_by' => $authUser->id,
            'employee_id' => $request->input('employee'),
            'status' => $request->input('status'),
            'description' => $request->input('description'),
            'incident_id' => $request->incidentId,
        ];

        $logincident = LogIncident::create($logIncidentData);

        return redirect()->route('incidents.index')->with('success', 'Cambio de Estatus de Incidencia Exitoso');
    }

    public function getEmployees()
    {
        $authUser = auth()->user();

        $employees = Employee::where('locality_id', $authUser->locality_id)->get();

        return view('incidents.index', compact('incidents', 'categories','employees'));
    }
}
