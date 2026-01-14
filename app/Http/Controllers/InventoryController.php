<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Locality;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\InventoryCategory;
use App\Models\LogInventory;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $user = Auth::user();
        $userLocalityId = $user->locality_id;

        $components = Inventory::with([
            'locality',
            'creator',
            'category',
            'logs' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'logs.creator'
        ])
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('material', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%")
                    ->orWhereHas('locality', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('creator', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('category', function ($q) use ($search) {
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
        $categories = InventoryCategory::where('locality_id', $userLocalityId)
            ->orWhereNull('locality_id')
            ->orderByRaw('locality_id IS NULL DESC')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('inventory.index', compact('components', 'localities', 'users', 'categories'));
    }

    public function create()
    {
        $user = Auth::user();
        $localities = Locality::select('id', 'name')->get();
        $users = User::select('id', 'name')->get();
        $categories = InventoryCategory::where('locality_id', $user->locality_id)->get();

        return view('inventory.create', compact('localities', 'users', 'user', 'categories'));
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
            'inventory_category_id' => 'required|exists:inventory_categories,id',
            'material' => 'nullable|string|max:50',
            'dimensions' => 'nullable|string|max:50',
        ]);

        Inventory::create($request->all());

        return redirect()->route('inventory.index')->with('success', 'Componente creado con éxito.');
    }

    public function show($id)
    {
        $component = Inventory::with(['locality', 'creator', 'category'])->findOrFail($id);
        return view('inventory.show', compact('component'));
    }

    public function edit($id)
    {
        $component = Inventory::findOrFail($id);
        $user = Auth::user();
        $localities = Locality::select('id', 'name')->get();
        $users = User::select('id', 'name')->get();
        $categories = InventoryCategory::where('locality_id', $user->locality_id)->get();

        return view('inventory.edit', compact('component', 'localities', 'users', 'user', 'categories'));
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
            'inventory_category_id' => 'required|exists:inventory_categories,id',
            'material' => 'nullable|string|max:50',
            'dimensions' => 'nullable|string|max:50',
        ]);

        $component = Inventory::findOrFail($id);
        $component->update($request->all());

        return redirect()->route('inventory.index')->with('success', 'Componente actualizado con éxito.');
    }

    public function destroy($id)
    {
        $component = Inventory::findOrFail($id);
        $component->delete();

        return redirect()->route('inventory.index')->with('success', 'Componente eliminado con éxito.');
    }

    public function generateInventoryPdf(Request $request)
    {
        set_time_limit(300);
        $authUser = Auth::user();
        $query = Inventory::with('category')->where('locality_id', $authUser->locality_id);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('material', 'like', "%{$search}%")
                    ->orWhere('id', $search)
                    ->orWhereHas('category', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $components = $query->get();
        $pdf = Pdf::loadView('reports.pdfInventory', compact('components', 'authUser'))
            ->setPaper('A4', 'portrait');
        return $pdf->stream('inventario.pdf');
    }
    public function updateAmount(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required|exists:inventory,id',
            'amount' => 'required|integer|min:0',
            'description' => 'required|string|max:500'
        ]);

        $authUser = auth()->user();
        $inventory = Inventory::findOrFail($request->inventory_id);

        if ($inventory->locality_id != $authUser->locality_id) {
            return redirect()->back()->with('error', 'No tienes permisos para actualizar este inventario');
        }

        $previousAmount = $inventory->amount;

        $difference = $request->amount - $previousAmount;

        LogInventory::create([
            'inventory_id' => $inventory->id,
            'locality_id' => $authUser->locality_id,
            'created_by' => $authUser->id,
            'previous_amount' => $previousAmount,
            'amount' => $request->amount,
            'description' => $request->description
        ]);

        $inventory->amount = $request->amount;
        $inventory->save();

        return redirect()->back()->with('success', 'Cantidad actualizada correctamente');
    }
}
