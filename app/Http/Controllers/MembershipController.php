<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $memberships = Membership::when($search, function($query, $search) {
            return $query->where('name', 'like', '%'.$search.'%');
        })->paginate(10);

        return view('memberships.index', compact('memberships'));
    }

    public function create()
    {
        return view('memberships.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'term_months' => 'required|integer|min:1'
        ]);

        Membership::create([
            'name' => $request->name,
            'price' => $request->price,
            'term_months' => $request->term_months,
            'created_by' => auth()->id()
        ]);

        return redirect()->route('memberships.index')->with('success', 'Membresía creada exitosamente.');
    }

    public function show(Membership $membership)
    {
        return view('memberships.show', compact('membership'));
    }

    public function edit(Membership $membership)
    {
        return view('memberships.edit', compact('membership'));
    }

    public function update(Request $request, Membership $membership)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'term_months' => 'required|integer|min:1'
        ]);

        $membership->update($request->all());

        return redirect()->route('memberships.index')->with('success', 'Membresía actualizada exitosamente.');
    }

    public function destroy(Membership $membership)
    {
        $membership->delete();
        return redirect()->route('memberships.index')->with('success', 'Membresía eliminada exitosamente.');
    }

    public function generateMembershipListReport()
    {
        $memberships = Membership::with('creator')->get();
        
        return view('memberships.report', compact('memberships'));
    }
}
