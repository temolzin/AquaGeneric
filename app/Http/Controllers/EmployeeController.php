<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Barryvdh\DomPDF\Facade\Pdf;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $authUser = auth()->user();

        $query = Employee::where('locality_id', $authUser->locality_id)->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereRaw("CONCAT(name, ' ', last_name) LIKE ?", ["%{$search}%"])
                ->orWhere('id', 'LIKE', "%{$search}%");
        }

        $employees = $query->paginate(10);
        return view('employees.index', compact('employees'));
    }
    public function store(Request $request)
    {
        $authUser = auth()->user();

        $validateData = $request->validate([
            'name' => 'required|string',
            'last_name' => 'required|string',
            'locality' => 'required|string',
            'zip_code' => 'required|string',
            'state' => 'required|string',
            'block' => 'required|string',
            'street' => 'required|string',
            'exterior_number' => 'required|string',
            'interior_number' => 'required|string',
            'email' => 'required|email|unique:employees,email',
            'phone_number' => 'nullable|string',
            'salary' => 'required',
        ]);

        $employeeData = $request->all();
        $employeeData['created_by'] = $authUser->id;
        $employeeData['locality_id'] = $authUser->locality_id;

        $employee = Employee::create($employeeData);

        if ($request->hasFile('photo')) {
            $employee->addMediaFromRequest('photo')->toMediaCollection('employeeGallery');
        }

        return redirect()->route('employees.index')->with('success', 'Empleado creado exitosamente.');
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        if ($employee) {
            $employee->name = $request->input('nameUpdate');
            $employee->last_name = $request->input('lastNameUpdate');
            $employee->locality = $request->input('localityUpdate');
            $employee->zip_code = $request->input('zipCodeUpdate');
            $employee->state = $request->input('stateUpdate');
            $employee->block = $request->input('blockUpdate');
            $employee->street = $request->input('streetUpdate');
            $employee->exterior_number = $request->input('exteriorNumberUpdate');
            $employee->interior_number = $request->input('interiorNumberUpdate');
            $employee->email = $request->input('emailUpdate');
            $employee->phone_number = $request->input('phoneNumberUpdate');
            $employee->salary = $request->input('salaryUpdate');

            $employee->save();

            if ($request->hasFile('photo')) {
                $employee->clearMediaCollection('employeeGallery');
                $employee->addMediaFromRequest('photo')->toMediaCollection('employeeGallery');
            }

            return redirect()->route('employees.index')->with('success', 'Empleado actualizado correctamente.');
        }

        return redirect()->back()->with('error', 'Empleado no encontrado.');
    }

    public function show($id)
    {
        $employee = Employee::findOrFail($id);
        return view('employees.show', compact('employee'));
    }
    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Empleado eliminado correctamente');
    }
    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        return view('employees.edit', compact('employee'));
    }
    public function generateEmployeeListPDF()
    {
        $authUser = auth()->user();
        $employees = Employee::where('locality_id', $authUser->locality_id)->get();
        $pdf = PDF::loadView('reports.generateEmployeeListPDF', compact('employees', 'authUser'))
            ->setPaper('A4', 'landscape');

        return $pdf->stream('employees.pdf');
    }

}
