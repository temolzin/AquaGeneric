<?php

namespace App\Http\Controllers;

use App\Models\EarningType;
use App\Models\Locality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EarningTypeController extends Controller
{
    public function index()
    {
        $earningTypes = EarningType::byUserLocality()
            ->with(['creator', 'locality'])
            ->orderByRaw('locality_id IS NULL DESC')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('earningTypes.index', compact('earningTypes'));
    }

    public function create()
    {
        return view('earningTypes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color_index' => 'required|integer|min:0|max:19',
        ]);

        EarningType::create([
            'name' => $request->name,
            'description' => $request->description,
            'color' => color($request->color_index),
            'locality_id' => Auth::user()->locality_id,
            'created_by' => Auth::id()
        ]);

        return redirect()->route('earningTypes.index')
            ->with('success', 'Tipo de Ingreso creado exitosamente.');
    }

    public function show(EarningType $earningType)
    {
        $this->authorizeLocality($earningType);
        return view('earningTypes.show', compact('earningType'));
    }

    public function edit(EarningType $earningType)
    {
        $this->authorizeLocality($earningType);
        $localities = Locality::all();
        return view('earningTypes.edit', compact('earningType', 'localities'));
    }

    public function update(Request $request, EarningType $earningType)
    {
        $this->authorizeLocality($earningType);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color_index' => 'required|integer|min:0|max:19',
        ]);

        $earningType->update([
            'name' => $request->name,
            'description' => $request->description,
            'color' => color($request->color_index),
        ]);

        return redirect()->route('earningTypes.index')
            ->with('success', 'Tipo de ingreso actualizado exitosamente.');
    }

    public function destroy(EarningType $earningType)
    {
        $this->authorizeLocality($earningType);
        $earningType->delete();

        return redirect()->route('earningTypes.index')
            ->with('success', 'Tipo de ingreso eliminado exitosamente.');
    }

    private function authorizeLocality(EarningType $earningType)
    {
        $user = Auth::user();
        if ($user->locality_id && $earningType->locality_id !== $user->locality_id) {
            abort(403, 'No tienes permisos para acceder a este recurso.');
        }
    }
}
