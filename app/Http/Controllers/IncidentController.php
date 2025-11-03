<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Incident;
use App\Models\IncidentCategory;
use App\Models\Employee;
use App\Models\IncidentStatus;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\LogIncident;

class IncidentController extends Controller
{
    public function index(Request $request)
    {
        $authUser = auth()->user();

        $query = Incident::with('incidentCategory', 'getstatusChangeLogs.employee')
        ->where('locality_id', $authUser->locality_id);

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $incidents = $query->paginate(10);

        $categories = IncidentCategory::where(function ($query) use ($authUser) {
            $query->where('locality_id', $authUser->locality_id)
                  ->orWhereNull('locality_id');
        })->get();

        $employees = Employee::where('locality_id', $authUser->locality_id)->get();
        $statuses = IncidentStatus::where('locality_id', $authUser->locality_id)->get();

        return view('incidents.index', compact('incidents', 'categories', 'employees', 'statuses'));
    }

    public function create()
    {

    }

    public function store(Request $request)
    {
        $authUser = auth()->user();

        $incidentData = [
            'name' => $request->input('name'),
            'start_date' => $request->input('startDate'),
            'description' => $request->input('description'),
            'category_id' => $request->input('category'),
            'status_id' => $request->input('statusUpdate'),
            'locality_id' => $authUser->locality_id,
            'created_by' => $authUser->id,
        ];

        $incident = Incident::create($incidentData);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $incident->addMedia($image)->toMediaCollection('incidentImages');
            }
        }

        return redirect()->route('incidents.index')->with('success', 'Incidente creado exitosamente.');
    }

    public function show($id)
    {

    }

    public function edit($id)
    {

    }

    public function update(Request $request, $id)
    {
        $incident = Incident::find($id);
        if ($incident) {
            $incident->name = $request->input('nameUpdate');
            $incident->start_date = $request->input('startDateUpdate');
            $incident->description = $request->input('descriptionUpdate');
            $incident->category_id = $request->input('categoryUpdate');
            $incident->status_id = $request->input('statusUpdate');

            $incident->save();

            if ($request->hasFile('imagesUpdate')) {
                $incident->clearMediaCollection('incidentImages');

                foreach ($request->file('imagesUpdate') as $image) {
                    $incident->addMedia($image)->toMediaCollection('incidentImages');
                }
            }

            return redirect()->route('incidents.index')->with('success', 'Incidencia actualizada correctamente.');
        }

        return redirect()->back()->with('error', 'Incidencia no encontrada.');
    }

    public function destroy(Incident $incident)
    {
        $incident->delete();
        return redirect()->route('incidents.index')->with('success', 'Incidencia eliminado exitosamente.');
    }

    public function generateIncidentListReport()
    {
        $authUser = auth()->user();
        $incidents = Incident::where('locality_id', $authUser->locality_id)->get();
        $pdf = PDF::loadView('reports.generateIncidentListReport', compact('incidents', 'authUser'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('incidents.pdf');
    }

    public function updateStatus(Request $request)
    {
        try {
            \Log::info('updateStatus called', $request->all());

            $request->validate([
                'incident_id' => 'required|exists:incidents,id',
                'status_id' => 'required|exists:incident_statuses,id',
                'employee' => 'required|exists:employees,id',
                'description' => 'sometimes|string|max:500'
            ]);

            $authUser = auth()->user();
            $incident = Incident::findOrFail($request->incident_id);

            if ($incident->locality_id != $authUser->locality_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para actualizar esta incidencia'
                ], 403);
            }

            $newStatus = IncidentStatus::find($request->status_id);
            $previousStatus = IncidentStatus::find($incident->status_id);
            $previousStatusName = $previousStatus ? $previousStatus->status : 'Desconocido';
            $incident->status_id = $request->status_id;
            $incident->save();

            \Log::info('Incident updated', ['incident_id' => $incident->id, 'new_status' => $request->status_id]);

            $logDescription = $request->description ?: 'Cambio de estatus: ' . 
                $previousStatusName . ' → ' . $newStatus->status;

            $logIncident = LogIncident::create([
                'incident_id' => $incident->id,
                'status' => $newStatus->status, 
                'employee_id' => $request->employee,
                'description' => $logDescription,
                'created_by' => $authUser->id,
                'locality_id' => $authUser->locality_id,
            ]);

            \Log::info('Log created successfully:', [
                'log_id' => $logIncident->id, 
                'status_value' => $logIncident->status,
                'status_type' => gettype($logIncident->status)
            ]);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $logIncident->addMedia($image)->toMediaCollection('logIncidentImages');
                }
                \Log::info('Images saved:', ['count' => count($request->file('images'))]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Estatus actualizado correctamente'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in updateStatus: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estatus: ' . $e->getMessage()
            ], 500);
        }
    }
}
