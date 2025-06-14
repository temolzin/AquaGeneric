<?php

namespace App\Http\Controllers;

use App\Models\IncidentCategory;
use Illuminate\Http\Request;

class IncidentCategoriesController extends Controller
{
    public function index()
    {
        $authUser = auth()->user();
        $categories = IncidentCategory::where('locality_id', $authUser->locality_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('incidentCategories.index', compact('categories'));
    }

    public function create()
    {
        return view('incidentCategories.create');
    }

    public function store(Request $request)
    {
        $authUser = auth()->user();

        IncidentCategory::create(array_merge(
            $request->only(['name', 'description']),
            [
                'locality_id' => $authUser->locality_id,
                'created_by' => $authUser->id
            ]
        ));

        return redirect()->route('incidentCategories.index')->with('success', 'Categoría de incidencia creada exitosamente.');
    }

    public function show(IncidentCategory $incidentCategory)
    {
        return view('incidentCategories.show', compact('incidentCategory'));
    }

    public function edit(IncidentCategory $incidentCategory)
    {
        return view('incidentCategories.edit', compact('incidentCategory'));
    }

    public function update(Request $request, IncidentCategory $incidentCategory)
    {
        $incidentCategory->update($request->only(['name', 'description']));

        return redirect()->route('incidentCategories.index')->with('success', 'Categoría de incidencia actualizada exitosamente.');
    }

    public function destroy(IncidentCategory $incidentCategory)
    {
        $incidentCategory->delete();

        return redirect()->route('incidentCategories.index')->with('success', 'Categoría de incidencia eliminada exitosamente.');
    }
}
