<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Incident;
use App\Models\IncidentCategory;
use App\Models\Employee;
use App\Models\IncidentStatus;

class IncidentController extends Controller
{
    public function index(Request $request)
    {
        $authUser = auth()->user();

        $query = Incident::with('incidentCategory');

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $incidents = $query->paginate(10);

        $categories = IncidentCategory::all();
        $employees = Employee::where('locality_id', $authUser->locality_id)->get();
        $statuses = IncidentStatus::pluck('status');

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
            'status' => $request->input('status'),
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
        $incident = incident::find($id);
        if ($incident) {
            $incident->name = $request->input('nameUpdate');
            $incident->start_date = $request->input('startDateUpdate');
            $incident->description = $request->input('descriptionUpdate');
            $incident->category_id = $request->input('categoryUpdate');
            $incident->status = $request->input('statusUpdate');

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
}
