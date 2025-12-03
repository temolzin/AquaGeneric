<?php

namespace App\Http\Controllers;

use App\Models\ExpenseType;
use App\Models\Locality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseTypeController extends Controller
{
    public function index()
    {
    $expenseTypes = ExpenseType::byUserLocality()
        ->with(['creator', 'locality'])
        ->orderByRaw('locality_id IS NULL DESC')
        ->orderBy('created_at', 'desc')
        ->paginate(10); 

    return view('expenseTypes.index', compact('expenseTypes'));
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

        ExpenseType::create([
            'name' => $request->name,
            'description' => $request->description,
            'color' => color($request->color_index),
            'locality_id' => Auth::user()->locality_id,
            'created_by' => Auth::id()
        ]);

        return redirect()->route('expenseTypes.index')
            ->with('success', 'Tipo de gasto creado exitosamente.');
    }

    public function show(ExpenseType $expenseType)
    {
        $this->authorizeLocality($expenseType);
        return view('expenseTypes.show', compact('expenseType'));
    }

    public function edit(ExpenseType $expenseType)
    {
        $this->authorizeLocality($expenseType);
        $localities = Locality::all();
        return view('expenseTypes.edit', compact('expenseType', 'localities'));
    }

    public function update(Request $request, ExpenseType $expenseType)
    {
    $this->authorizeLocality($expenseType);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color_index' => 'required|integer|min:0|max:19',
        ]);

        $expenseType->update([
            'name' => $request->name,
            'description' => $request->description,
            'color' => color($request->color_index),
        ]);

        return redirect()->route('expenseTypes.index')
            ->with('success', 'Tipo de gasto actualizado exitosamente.');
    }

    public function destroy(ExpenseType $expenseType)
    {
    $this->authorizeLocality($expenseType);
    $expenseType->delete();

    return redirect()->route('expenseTypes.index')
        ->with('success', 'Tipo de gasto eliminado exitosamente.');
    }

    private function authorizeLocality(ExpenseType $expenseType)
    {
        $user = Auth::user();
        if ($user->locality_id && $expenseType->locality_id !== $user->locality_id) {
            abort(403, 'No tienes permisos para acceder a este recurso.');
        }
    }
}
