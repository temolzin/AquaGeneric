<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FaultReport;
use Illuminate\Support\Facades\Auth;

class CustomerFaultReportController extends Controller
{
    public function index(Request $request)
    {
        $authUser = Auth::user();

        $query = FaultReport::where('created_by', $authUser->id)
            ->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('id', 'LIKE', "%{$search}%")
                    ->orWhere('status', 'LIKE', "%{$search}%");
            });
        }

        $reports = $query->paginate(10);

        return view('customerFaultReports.index', compact('reports'));
    }

    public function create()
    {
        return view('customerFaultReports.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
        ]);

        $report = new FaultReport();
        $report->title = $request->title;
        $report->description = $request->description;
        $report->status = 'Earring';
        $report->date_report = now();
        $report->locality_id = Auth::user()->locality_id;
        $report->created_by = Auth::id();
        $report->save();

        return redirect()->route('customerFaultReports.index')
                        ->with('success', 'Reporte de falla registrado correctamente.');
    }

    public function show($id)
    {
        $report = FaultReport::where('created_by', Auth::id())->findOrFail($id);
        return view('customerFaultReports.show', compact('report'));
    }

    public function edit($id)
    {
        $report = FaultReport::where('created_by', Auth::id())->findOrFail($id);
        return view('customerFaultReports.edit', compact('report'));
    }

    public function update(Request $request, $id)
    {
        $report = FaultReport::where('created_by', Auth::id())->findOrFail($id);

        $report->update([
            'title' => $request->input('titleUpdate'),
            'description' => $request->input('descriptionUpdate'),
            'date_report' => $request->input('dateReportUpdate'),
            'updated_at' => now(),
        ]);

        return redirect()->route('customerFaultReports.index')
            ->with('success', 'Reporte de falla actualizado correctamente.');
    }

    public function destroy($id)
    {
        $report = FaultReport::where('created_by', Auth::id())->findOrFail($id);
        $report->delete([
            'deleted_at' => now(),
        ]);
        return redirect()->route('customerFaultReports.index')
            ->with('success', 'Reporte de falla eliminado correctamente.');
    }
}
