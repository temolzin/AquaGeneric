<?php

namespace App\Http\Controllers;

use App\Models\Cost;
use Illuminate\Http\Request;

class CostController extends Controller
{
    public function index()
    {
        $user = auth()->user()->locality_id;
        $costs = Cost::where('locality_id', $user->locality_id)->get();
        return view('costs.index', compact('costs'));
    }

    public function create()
    {
        return view('costs.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        Cost::create(array_merge(
            $request->all(),
            [
                'locality_id' => $user->locality_id,
                'created_by' => $user->id
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
}
