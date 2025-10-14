<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\Locality;

class SectionController extends Controller
{
    public function index(Request $request)
    {
        $authUser = auth()->user();
        $query = Section::where('sections.locality_id', $authUser->locality_id)
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
            'locality_id' => 'required|exists:localities,id',
            'name' => 'required|string|max:100',
            'zip_code' => 'required|string|max:5',
            'color' => 'required|string|max:20',
        ]);

        Section::create([
            'locality_id' => $request->locality_id,
            'created_by' => $authUser->id,
            'name' => $request->name,
            'zip_code' => $request->zip_code,
            'color' => $request->color,
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
        $localities = Locality::all();

        return view('sections.edit', compact('section', 'localities'));
    }

    public function update(Request $request, $id)
    {
        $section = Section::find($id);

        if (!$section) {
            return redirect()->back()->with('error', 'Sección no encontrada.');
        }

        $request->validate([
            'locality_id' => 'required|exists:localities,id',
            'name' => 'required|string|max:100',
            'zip_code' => 'required|string|max:5',
            'color' => 'required|string|max:20',
        ]);

        $section->update([
            'locality_id' => $request->locality_id,
            'name' => $request->name,
            'zip_code' => $request->zip_code,
            'color' => $request->color,
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
}
