<?php

namespace App\Http\Controllers;

use App\Models\InventoryCategory;
use App\Models\Locality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryCategoryController extends Controller
{
    public function index()
    {
        $inventoryCategories = InventoryCategory::byUserLocality()
            ->with(['creator', 'locality'])
            ->orderBy('name')
            ->paginate(10); 

        return view('inventoryCategories.index', compact('inventoryCategories'));
    }

    public function create()
    {
        return view('inventoryCategories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
        ]);

        InventoryCategory::create([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
            'locality_id' => Auth::user()->locality_id, 
            'created_by' => Auth::id()
        ]);

        return redirect()->route('inventoryCategories.index')
            ->with('success', 'Categoría de inventario creada exitosamente.');
    }

    public function show(InventoryCategory $inventoryCategory)
    {
        return view('inventoryCategories.show', compact('inventoryCategory'));
    }

    public function edit(InventoryCategory $inventoryCategory)
    {
        $localities = Locality::all();
        return view('inventoryCategories.edit', compact('inventoryCategory', 'localities'));
    }

    public function update(Request $request, InventoryCategory $inventoryCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
        ]);

        $inventoryCategory->update([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
        ]);

        return redirect()->route('inventoryCategories.index')
            ->with('success', 'Categoría de inventario actualizada exitosamente.');
    }

    public function destroy(InventoryCategory $inventoryCategory)
    {
        $inventoryCategory->delete();

        return redirect()->route('inventoryCategories.index')
            ->with('success', 'Categoría de inventario eliminada exitosamente.');
    }
}
