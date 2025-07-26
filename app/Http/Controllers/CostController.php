<?php

namespace App\Http\Controllers;

use App\Models\Cost;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CostController extends Controller
{
    public function index()
    {
        $authUser = auth()->user();
        $costs = Cost::where('locality_id', $authUser->locality_id)
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
        $cost->update($request->all());

        return redirect()->route('costs.index')->with('success', 'Costo actualizado exitosamente.');
    }

    public function destroy(Cost $cost)
    {
        $cost->delete();

        return redirect()->route('costs.index')->with('success', 'Costo eliminado exitosamente.');
    }

    public function generateCostListReport()
    {
        $authUser = auth()->user();
        $costs = cost::all();
        $pdf = PDF::loadView('reports.generateCostListReport', compact('costs', 'authUser'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('costs.pdf');
    }
}
