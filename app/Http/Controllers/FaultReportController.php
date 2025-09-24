<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FaultReport;

class FaultReportController extends Controller
{
    public function index(Request $request)
    {
        $authUser = auth()->user();

        $query = FaultReport::where('fault_report.locality_id', $authUser->locality_id)
            ->orderBy('fault_report.created_at', 'desc')
            ->select('fault_report.*');

        if ($request->has('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('fault_report.title', 'LIKE', "%{$search}%")
                    ->orWhere('fault_report.id', 'LIKE', "%{$search}%")
                    ->orWhere('fault_report.status', 'LIKE', "%{$search}%");
            });
        }

        $reports = $query->paginate(10);

        return view('faultReport.index', compact('reports'));
    }


    public function show($id)
    {
        $report = FaultReport::findOrFail($id);
        return view('faultReport.show', compact('report'));
    }

    public function update(Request $request, $id)
    {
        $report = FaultReport::find($id);

        if (!$report) {
            return redirect()->back()->with('error', 'Reporte de falla no encontrado.');
        }

        $report->title = $request->input('titleUpdate');
        $report->description = $request->input('descriptionUpdate');
        $report->status = $request->input('statusUpdate');
        $report->date_report = $request->input('dateReportUpdate');

        $report->save();

        return redirect()->route('faultReport.index')->with('success', 'Reporte de falla actualizado correctamente.');
    }

    public function destroy($id)
    {
        $report = FaultReport::find($id);

        if (!$report) {
            return redirect()->back()->with('error', 'Reporte de falla no encontrado.');
        }

        $report->delete();

        return redirect()->route('faultReport.index')->with('success', 'Reporte de falla eliminado correctamente.');
    }
}

