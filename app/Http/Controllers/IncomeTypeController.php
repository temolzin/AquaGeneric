<?php

namespace App\Http\Controllers;

use App\Models\IncomeType;
use App\Models\Locality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncomeTypeController extends Controller
{
    public function index()
    {
    $incomeTypes = IncomeType::byUserLocality()
        ->with(['creator', 'locality'])
        ->orderByRaw('locality_id IS NULL DESC')
        ->orderBy('created_at', 'desc')
        ->paginate(10); 

    return view('incomeTypes.index', compact('incomeTypes'));
    }

    public function create()
    {
        
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color_index' => 'required|integer|min:0|max:19',
        ]);

        IncomeType::create([
            'name' => $request->name,
            'description' => $request->description,
            'color' => color($request->color_index),
            'locality_id' => Auth::user()->locality_id,
            'created_by' => Auth::id()
        ]);

        return redirect()->route('incomeTypes.index')
            ->with('success', 'Tipo de Ingreso creado exitosamente.');
    }

    public function show(IncomeType $incomeType)
    {
        $this->authorizeLocality($incomeType);
        return view('ingresoTypes.show', compact('incomeType'));
    }

    public function edit(IncomeType $incomeType)
    {
        $this->authorizeLocality($incomeType);
        $localities = Locality::all();
        return view('incomeTypes.edit', compact('incomeType', 'localities'));
    }

    public function update(Request $request, IncomeType $incomeType)
    {
    $this->authorizeLocality($incomeType);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color_index' => 'required|integer|min:0|max:19',
        ]);

        $incomeType->update([
            'name' => $request->name,
            'description' => $request->description,
            'color' => color($request->color_index),
        ]);

        return redirect()->route('incomeTypes.index')
            ->with('success', 'Tipo de gasto actualizado exitosamente.');
    }

    public function destroy(IncomeType $incomeType)
    {
    $this->authorizeLocality($incomeType);
    $incomeType->delete();

    return redirect()->route('incomeTypes.index')
        ->with('success', 'Tipo de gasto eliminado exitosamente.');
    }

    private function authorizeLocality(IncomeType $incomeType)
    {
        $user = Auth::user();
        if ($user->locality_id && $incomeType->locality_id !== $user->locality_id) {
            abort(403, 'No tienes permisos para acceder a este recurso.');
        }
    }
}
