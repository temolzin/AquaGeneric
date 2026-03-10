<?php

namespace App\Http\Controllers;

use App\Models\Cost;
use App\Models\MovementHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CostController extends Controller
{
    public function index()
    {
        $query = Cost::with('creator')
            ->orderByRaw('locality_id IS NULL DESC')
            ->orderBy('created_at', 'desc');
        
        if (request()->has('search') && request('search') != '') {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('category', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('price', 'like', "%{$search}%");
            });
        }
        
        $costs = $query->paginate(10)->appends(request()->query());
        
        return view('costs.index', compact('costs'));
    }

    public function create()
    {
        return view('costs.create');
    }

    public function store(Request $request)
    {
        $authUser = auth()->user();

        Cost::create(array_merge(
            $request->all(),
            [
                'locality_id' => $authUser->locality_id,
                'created_by' => $authUser->id
            ]
        ));

        return redirect()->route('costs.index')->with('success', 'Costo creado exitosamente.');
    }

    public function show(Cost $cost)
    {
        return view('costs.show', compact('cost'));
    }

    public function edit(Cost $cost)
    {
        return view('costs.edit', compact('cost'));
    }

    public function update(Request $request, Cost $cost)
    {
        $before = $cost->toArray();
        $cost->update($request->all());
        $after = $cost->fresh()->toArray();

        MovementHistory::create([
        'alter_by'    => Auth::user()->id,
        'module'      => 'costos',
        'action'      => 'update',
        'record_id'   => $cost->id,
        'before_data' => $before,
        'current_data'=> $after,
    ]);

        return redirect()->route('costs.index')->with('success', 'Costo actualizado exitosamente.');
    }

    public function destroy(Cost $cost)
    {
        $before = $cost->toArray();
        $cost->delete();

        MovementHistory::create([
        'alter_by'     => Auth::user()->id,
        'module'       => 'costos',
        'action'       => 'delete',
        'record_id'    => $cost->id,
        'before_data'  => $before,
        'current_data' => null,
    ]);

        return redirect()->route('costs.index')->with('success', 'Costo eliminado exitosamente.');
    }

    public function generateCostListReport()
    {
        $authUser = auth()->user();
        $costs = cost::where('locality_id', $authUser->locality_id)
                    ->orWhereNull('locality_id')
                    ->orderByRaw('locality_id IS NULL DESC')
                    ->orderBy('created_at', 'desc')
                    ->get();
        
        $pdf = PDF::loadView('reports.generateCostListReport', compact('costs', 'authUser'))      
            ->setPaper('A4', 'portrait');

        return $pdf->stream('costs.pdf');
    }
}
