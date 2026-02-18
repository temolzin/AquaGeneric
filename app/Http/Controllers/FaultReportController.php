<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FaultReport;
use App\Models\LogFaultReport;
use Illuminate\Support\Facades\Auth;

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

    public function updateStatus(Request $request)
    {
        try {
            \Log::info('updateStatus called', $request->all());

            $request->validate([
                'fault_report_id' => 'required|exists:fault_report,id',
                'status' => 'required|string|in:Pendiente,En revisión,Completado',
                'comentario' => 'sometimes|string|max:500'
            ]);

            $authUser = auth()->user();
            $report = FaultReport::findOrFail($request->fault_report_id);

            if ($report->locality_id != $authUser->locality_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para actualizar este reporte'
                ], 403);
            }

            $previousStatus = $report->status;
            $report->status = $request->status;
            $report->save();

            \Log::info('Fault Report updated', ['report_id' => $report->id, 'new_status' => $request->status]);

            $logDescription = $request->comentario ?: 'Cambio de estatus: ' . 
                $previousStatus . ' → ' . $request->status;

            $logFaultReport = LogFaultReport::create([
                'fault_report_id' => $report->id,
                'status' => $request->status,
                'comentario' => $logDescription,
                'created_by' => $authUser->id,
                'locality_id' => $authUser->locality_id,
            ]);

            \Log::info('Log created successfully:', [
                'log_id' => $logFaultReport->id, 
                'status_value' => $logFaultReport->status,
                'status_type' => gettype($logFaultReport->status)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Estatus actualizado correctamente'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in updateStatus: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estatus: ' . $e->getMessage()
            ], 500);
        }
    }
}
