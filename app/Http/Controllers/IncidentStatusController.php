<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IncidentStatus;
use Barryvdh\DomPDF\Facade\Pdf;

class IncidentStatusController extends Controller
{
    public function index()
    {
        $authUser = auth()->user();

        $statuses = IncidentStatus::where('locality_id', $authUser->locality_id)->paginate(10);

        return view('incidentStatuses.index', compact('statuses'));
    }

    public function create()
    {
        return view('incidentStatuses.create');
    }

    public function store(Request $request)
    {
        $authUser = auth()->user();

        $request->validate([
            'status' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string', 
        ]);

        IncidentStatus::create(
            array_merge(
                $request->only(['status', 'description', 'color']), 
                [
                    'locality_id' => $authUser->locality_id,
                    'created_by' => $authUser->id,
                ]
            )
        );

        return redirect()->route('incidentStatuses.index')->with('success', 'Estatus creado exitosamente.');
    }

    public function edit(IncidentStatus $incidentStatus)
    {
        return view('incidentStatuses.edit', compact('incidentStatus'));
    }

    public function update(Request $request, IncidentStatus $incidentStatus)
    {
        $request->validate([
            'status' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string', 
        ]);

        $incidentStatus->update($request->only('status', 'description', 'color')); 
        return redirect()->route('incidentStatuses.index')
            ->with('success', 'Estatus actualizado correctamente.');
    }

    public function destroy(IncidentStatus $incidentStatus)
    {
        $incidentStatus->delete();
        return redirect()->route('incidentStatuses.index')
            ->with('success', 'Estatus eliminado correctamente.');
    }

    public function generateIncidentStatusListReport()
    {
        $authUser = auth()->user();
        $incidentStatus = IncidentStatus::all();
        $pdf = PDF::loadView('reports.generateIncidentStatusListReport', compact('incidentStatus', 'authUser'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('incidentStatus.pdf');
    }
}
