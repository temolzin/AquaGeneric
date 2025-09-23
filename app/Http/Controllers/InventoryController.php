<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Locality;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $user = Auth::user();
        $userLocalityId = $user->locality_id;

        $componentes = Inventory::with(['locality', 'creator'])
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhereHas('locality', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('creator', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->when($userLocalityId, function ($query) use ($userLocalityId) {
                return $query->where('locality_id', $userLocalityId);
            })
            ->whereNull('deleted_at')
            ->paginate(10);

        $localities = Locality::select('id', 'name')->get();
        $users = User::select('id', 'name')->get();

        return view('inventory.index', compact('componentes', 'localities', 'users'));
    }

    public function create()
    {
        $localities = Locality::select('id', 'name')->get();
        $users = User::select('id', 'name')->get();
        $user = Auth::user();
        return view('inventory.create', compact('localities', 'users', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $request->merge([
            'locality_id' => $user->locality_id,
            'created_by' => $user->id,
        ]);

        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'amount' => 'required|integer|min:0',
            'category' => 'required|string|max:50',
            'material' => 'nullable|string|max:50',
            'dimensions' => 'nullable|string|max:50',
        ]);

        Inventory::create($request->all());

        return redirect()->route('inventory.index')->with('success', 'Componente creado con éxito.');
    }

    public function show($id)
    {
        $componente = Inventory::with(['locality', 'creator'])->findOrFail($id);
        return view('inventory.show', compact('componente'));
    }

    public function edit($id)
    {
        $componente = Inventory::findOrFail($id);
        $localities = Locality::select('id', 'name')->get();
        $users = User::select('id', 'name')->get();
        $user = Auth::user();
        return view('inventory.edit', compact('componente', 'localities', 'users', 'user'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $request->merge([
            'locality_id' => $user->locality_id,
            'created_by' => $user->id,
        ]);

        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'amount' => 'required|integer|min:0',
            'category' => 'required|string|max:50',
            'material' => 'nullable|string|max:50',
            'dimensions' => 'nullable|string|max:50',
        ]);

        $componente = Inventory::findOrFail($id);
        $componente->update($request->all());

        return redirect()->route('inventory.index')->with('success', 'Componente actualizado con éxito.');
    }

    public function destroy($id)
    {
        $componente = Inventory::findOrFail($id);
        $componente->delete();

        return redirect()->route('inventory.index')->with('success', 'Componente eliminado con éxito.');
    }
}
