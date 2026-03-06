<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

class MembershipController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $memberships = Membership::with('creator')
            ->when($search, function($query, $search) {
                return $query->where('name', 'like', '%'.$search.'%');
            })
            ->paginate(10);

        return view('memberships.index', compact('memberships'));
    }

    public function create()
    {
        return view('memberships.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:memberships,name'],
            'price' => 'required|numeric|min:0',
            'term_months' => 'required|integer|min:1',
            'water_connections_number' => 'required|integer|min:0',
            'users_number' => 'required|integer|min:0'
        ], [
            'name.unique' => 'Ya existe una membresía con ese nombre.',
            'name.required' => 'El nombre de la membresía es obligatorio.',
            'price.required' => 'El precio es obligatorio.',
            'term_months.required' => 'La duración es obligatoria.',
        ]);

        try {
            Membership::create([
                'created_by' => Auth::id(),
                'name' => $request->name,
                'price' => $request->price,
                'term_months' => $request->term_months,
                'water_connections_number' => $request->water_connections_number,
                'users_number' => $request->users_number
            ]);

        return redirect()->route('memberships.index')->with('success', 'Membresía creada exitosamente.');

        } catch (QueryException $e) {
            if (isset($e->errorInfo[1]) && (int)$e->errorInfo[1] === 1062) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Ya existe una membresía con ese nombre.');
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Hubo un problema al crear la membresía. Intenta nuevamente.');
        }
    }

    public function show(Membership $membership)
    {
        $membership->load('creator');
        return view('memberships.show', compact('membership'));
    }

    public function edit(Membership $membership)
    {
        return view('memberships.edit', compact('membership'));
    }

    public function update(Request $request, Membership $membership)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255',  Rule::unique('memberships', 'name')->ignore($membership->id)],
            'price' => 'required|numeric|min:0',
            'term_months' => 'required|integer|min:1',
            'water_connections_number' => 'required|integer|min:0',
            'users_number' => 'required|integer|min:0'
        ], [
            'name.unique' => 'Ya existe una membresía con ese nombre.',
            'name.required' => 'El nombre de la membresía es obligatorio.',
            'price.required' => 'El precio es obligatorio.',
            'term_months.required' => 'La duración es obligatoria.',
        ]);

        try{
        $membership->update([
            'name' => $request->name,
            'price' => $request->price,
            'term_months' => $request->term_months,
            'water_connections_number' => $request->water_connections_number,
            'users_number' => $request->users_number
        ]);

        return redirect()->route('memberships.index')->with('success', 'Membresía actualizada exitosamente.');

        } catch (QueryException $e) {
            if (isset($e->errorInfo[1]) && (int)$e->errorInfo[1] === 1062) {
                return redirect()->back()->withInput()->with('error', 'Ya existe una membresía con ese nombre.');
            }

            return redirect()->back()->withInput()->with('error', 'Hubo un problema al crear la membresía. Intenta nuevamente.');
        }
    }

    public function destroy(Membership $membership)
    {
        if ($membership->hasDependencies()) {
            return redirect()->route('memberships.index')
                ->with('error', 'No se puede eliminar la membresía porque tiene localidades asociadas.');
        }
        $membership->delete();

        return redirect()->route('memberships.index')
        ->with('success', 'Membresía eliminada exitosamente.');
    }

    public function generateMembershipListReport()
    {
        $memberships = Membership::with('creator')->get();

        return view('memberships.report', compact('memberships'));
    }
}
