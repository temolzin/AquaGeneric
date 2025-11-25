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
        $authUser = auth()->user();
        $costs = Cost::where(function ($q) use ($authUser) {
                $q->where('locality_id', $authUser->locality_id)
                    ->orWhereNull('locality_id');
            })
            ->whereHas('creator', function ($query) use ($authUser) {
                $query->where('locality_id', $authUser->locality_id);
            
            })
            ->with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
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
                    ->orderBy('created_at', 'desc')
                    ->get();
        
        $pdf = PDF::loadView('reports.generateCostListReport', compact('costs', 'authUser'))      
            ->setPaper('A4', 'portrait');

        return $pdf->stream('costs.pdf');
    }
}
