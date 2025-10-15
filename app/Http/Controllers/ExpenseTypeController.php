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
        $expenseTypes = ExpenseType::with(['creator', 'locality'])
            ->orderBy('name')
            ->paginate(10); 

        return view('expenseTypes.index', compact('expenseTypes'));
    }

    public function create()
    {
        return view('expenseTypes.create'); 
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
        ]);

        ExpenseType::create([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
            'created_by' => Auth::id()
        ]);

        return redirect()->route('expenseTypes.index')
            ->with('success', 'Tipo de gasto creado exitosamente.');
    }

    public function show(ExpenseType $expenseType)
    {
        return view('expenseTypes.show', compact('expenseType'));
    }

    public function edit(ExpenseType $expenseType)
    {
        return view('expenseTypes.edit', compact('expenseType'));
    }

    public function update(Request $request, ExpenseType $expenseType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
        ]);

        $expenseType->update([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
        ]);

        return redirect()->route('expenseTypes.index')
            ->with('success', 'Tipo de gasto actualizado exitosamente.');
    }

    public function destroy(ExpenseType $expenseType)
    {
        $expenseType->delete();

        return redirect()->route('expenseTypes.index')
            ->with('success', 'Tipo de gasto eliminado exitosamente.');
    }
}
