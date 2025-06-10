<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Incident;

class IncidentController extends Controller
{
    public function index()
    {
        $incidents = Incident::with('incidentCategory')->paginate(10);

        return view('incidents.index',compact('incidents'));
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
            $incident->category = $request->input('categoryUpdate');
            $incident->status = $request->input('statusUpdate');

            $incident->save();

            return redirect()->route('incidents.index')->with('success', 'Inci actualizado correctamente.');
        }

        return redirect()->back()->with('error', 'Cliente no encontrado.');
    }

    public function destroy(Incident $incident)
    {
        $incident->delete();
        return redirect()->route('incidents.index')->with('success', 'Incidencia eliminado exitosamente.');
    }
}
