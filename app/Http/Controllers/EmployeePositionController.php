<?php

namespace App\Http\Controllers;

use App\Models\EmployeePosition;
use App\Models\Locality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeePositionController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeePosition::byUserLocality()
            ->with(['creator', 'locality'])
            ->orderByRaw('locality_id IS NULL DESC')
            ->orderBy('created_at', 'desc');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $positions = $query->paginate(10)->appends($request->query());

        return view('employeePositions.index', compact('positions'));
    }

    public function create()
    {
        
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'color_index' => 'required|integer|min:0|max:19',
        ]);

        EmployeePosition::create([
            'name' => $request->name,
            'description' => $request->description,
            'color' => color($request->color_index),
            'locality_id' => Auth::user()->locality_id,
            'created_by' => Auth::id()
        ]);

        return redirect()->route('employeePositions.index')
            ->with('success', 'Posición de empleado creada exitosamente.');
    }

    public function show(EmployeePosition $employeePosition)
    {
        $this->authorizeLocality($employeePosition);
        return view('employeePositions.show', compact('employeePosition'));
    }

    public function edit(EmployeePosition $employeePosition)
    {
        $this->authorizeLocality($employeePosition);
        $localities = Locality::all();
        return view('employeePositions.edit', compact('employeePosition', 'localities'));
    }

    public function update(Request $request, EmployeePosition $employeePosition)
    {
        $this->authorizeLocality($employeePosition);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'color_index' => 'required|integer|min:0|max:19',
        ]);

        $employeePosition->update([
            'name' => $request->name,
            'description' => $request->description,
            'color' => color($request->color_index),
        ]);

        return redirect()->route('employeePositions.index')
            ->with('success', 'Posición de empleado actualizada exitosamente.');
    }

    public function destroy(EmployeePosition $employeePosition)
    {
        $this->authorizeLocality($employeePosition);

        if ($employeePosition->employees()->exists()) {
            return redirect()->route('employeePositions.index')
                ->with('error', 'No se puede eliminar esta posición porque tiene empleados asociados.');
        }

        $employeePosition->delete();

        return redirect()->route('employeePositions.index')
            ->with('success', 'Posición de empleado eliminada exitosamente.');
    }

    private function authorizeLocality(EmployeePosition $employeePosition)
    {
        $user = Auth::user();
        if ($user->locality_id && $employeePosition->locality_id !== $user->locality_id) {
            abort(403, 'No tienes permisos para acceder a este recurso.');
        }
    }
}
