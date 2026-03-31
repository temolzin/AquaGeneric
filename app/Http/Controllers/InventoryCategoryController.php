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
        $query = InventoryCategory::byUserLocality()
            ->with(['creator', 'locality'])
            ->orderByRaw('locality_id IS NULL DESC')
            ->orderBy('created_at', 'desc');

        if (request()->has('search') && request('search') != '') {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $inventoryCategories = $query->paginate(10)->appends(request()->query());

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
            'description' => 'required|string',
            'color_index' => 'required|integer|min:0|max:19',
        ]);

        InventoryCategory::create([
            'name' => $request->name,
            'description' => $request->description,
            'color' => color($request->color_index),
            'locality_id' => Auth::user()->locality_id,
            'created_by' => Auth::id(),
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
            'description' => 'required|string',
            'color_index' => 'required|integer|min:0|max:19',
        ]);

        $inventoryCategory->update([
            'name' => $request->name,
            'description' => $request->description,
            'color' => color($request->color_index),
        ]);

        return redirect()->route('inventoryCategories.index')
            ->with('success', 'Categoría de inventario actualizada exitosamente.');
    }

    public function destroy(InventoryCategory $inventoryCategory)
    {
         if ($inventoryCategory->hasDependencies()) {
            return redirect()->route('inventoryCategories.index')
                ->with('error', 'No se puede eliminar la categoría porque tiene registros asociados.');
        }

        $inventoryCategory->delete();

        return redirect()->route('inventoryCategories.index')
            ->with('success', 'Categoría de inventario eliminada exitosamente.');
    }
}
