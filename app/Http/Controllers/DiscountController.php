<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\MovementHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function index()
    {
        $authUser = auth()->user();

        $query = Discount::withoutGlobalScope('byUserLocality')
            ->where(function ($q) use ($authUser) {
                $q->where('locality_id', $authUser->locality_id)
                  ->orWhereNull('locality_id');
            })
            ->with('creator')
            ->orderByRaw('locality_id IS NULL DESC')
            ->orderBy('created_at', 'desc');

        if (request()->has('search') && request('search') != '') {
            $search = request('search');

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('percentage', 'like', "%{$search}%");
            });
        }

        $discounts = $query->paginate(10)->appends(request()->query());

        return view('discounts.index', compact('discounts'));
    }

    public function create()
    {
        return view('discounts.create');
    }

    public function store(Request $request)
    {
        $authUser = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'percentage' => 'required|numeric|min:0|max:100',
            'color' => 'required|string|max:20',
            'description' => 'nullable|string',
        ]);

        Discount::create([
            'name' => $request->name,
            'percentage' => $request->percentage,
            'color' => $request->color,
            'description' => $request->description,
            'locality_id' => $authUser->locality_id,
            'created_by' => $authUser->id,
        ]);

        return redirect()->route('discounts.index')
            ->with('success', 'Descuento creado exitosamente.');
    }

    public function show(Discount $discount)
    {
        return view('discounts.show', compact('discount'));
    }

    public function edit(Discount $discount)
    {
        return view('discounts.edit', compact('discount'));
    }

    public function update(Request $request, Discount $discount)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'percentage' => 'required|numeric|min:0|max:100',
            'color' => 'required|string|max:20',
            'description' => 'nullable|string',
        ]);

        $before = $discount->toArray();

        $discount->update($request->all());

        $after = $discount->fresh()->toArray();

        MovementHistory::create([
            'alter_by'     => Auth::user()->id,
            'module'       => 'descuentos',
            'action'       => 'update',
            'record_id'    => $discount->id,
            'before_data'  => $before,
            'current_data' => $after,
        ]);

        return redirect()->route('discounts.index')
            ->with('success', 'Descuento actualizado exitosamente.');
    }

    public function destroy(Discount $discount)
    {
        $before = $discount->toArray();

        $discount->delete();

        MovementHistory::create([
            'alter_by'     => Auth::user()->id,
            'module'       => 'descuentos',
            'action'       => 'delete',
            'record_id'    => $discount->id,
            'before_data'  => $before,
            'current_data' => null,
        ]);

        return redirect()->route('discounts.index')
            ->with('success', 'Descuento eliminado exitosamente.');
    }

    public function generateDiscountListReport()
    {
        $authUser = auth()->user();

        $discounts = Discount::where('locality_id', $authUser->locality_id)
            ->orWhereNull('locality_id')
            ->orderByRaw('locality_id IS NULL DESC')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
