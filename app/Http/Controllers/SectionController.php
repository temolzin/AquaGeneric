<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\Locality;
use PDF;

class SectionController extends Controller
{
    public function index(Request $request)
    {
        $authUser = auth()->user();
        $query = Section::where('sections.locality_id', $authUser->locality_id)
            ->orWhereNull('locality_id')
            ->orderByRaw('locality_id IS NULL DESC')
            ->orderBy('sections.created_at', 'desc')
            ->select('sections.*');

        if ($request->has('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                if (is_numeric($search)) {
                    $q->where('sections.id', $search);
                }
                $q->orWhere('sections.name', 'LIKE', "%{$search}%");
            });
        }

        $sections = $query->paginate(10);

        return view('sections.index', compact('sections'));
    }

    public function store(Request $request)
    {
        $authUser = auth()->user();

        $request->validate([
            'name' => 'required|string|max:100',
            'zip_code' => 'required|string|max:5',
            'color_index' => 'required|integer|min:0|max:19',
        ]);

        Section::create([
            'locality_id' => $authUser->locality_id, 
            'created_by' => $authUser->id,
            'name' => $request->name,
            'zip_code' => $request->zip_code,
            'color' => color($request->color_index),
        ]);

        return redirect()->route('sections.index')->with('success', 'Sección creada correctamente.');
    }

    public function show($id)
    {
        $authUser = auth()->user();
        $section = Section::with('locality')
            ->where('id', $id)
            ->where('locality_id', $authUser->locality_id)
            ->firstOrFail();

        return view('sections.show', compact('section'));
    }

    public function edit($id)
    {
        $section = Section::findOrFail($id);

        return view('sections.edit', compact('section', 'localities'));
    }

    public function update(Request $request, $id)
    {
        $section = Section::find($id);

        if (!$section) {
            return redirect()->back()->with('error', 'Sección no encontrada.');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'zip_code' => 'required|string|max:5',
            'color_index' => 'required|integer|min:0|max:19',
        ]);

        $section->update([
            'name' => $request->name,
            'zip_code' => $request->zip_code,
            'color' => color($request->color_index),
        ]);

        return redirect()->route('sections.index')->with('success', 'Sección actualizada correctamente.');
    }

    public function destroy($id)
    {
        $section = Section::find($id);

        if (!$section) {
            return redirect()->back()->with('error', 'Sección no encontrada.');
        }

        $section->delete();

        return redirect()->route('sections.index')->with('success', 'Sección eliminada correctamente.');
    }

    public function pdfSections(Request $request)
    {
        $authUser = auth()->user();
        $sectionId = $request->input('section_id'); 
    
        if (!$sectionId) {
            return redirect()->back()->with('error', 'Debe seleccionar una sección.');
        }
    
        $section = Section::where('id', $sectionId)
            ->where('locality_id', $authUser->locality_id) 
            ->orWhereNull('locality_id')
            ->with(['waterConnections.customer', 'waterConnections.cost', 'locality']) 
            ->firstOrFail();
    
        $pdf = PDF::loadView('reports.pdfSections', compact('section', 'authUser'))
            ->setPaper('A4', 'portrait');

        $fileName = 'Tomas_de_agua_de_la_' . str_replace(' ', '_', $section->name) . '.pdf';
        
        return $pdf->stream($fileName);
    }
}
