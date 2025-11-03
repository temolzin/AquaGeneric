<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Debt;
use App\Models\Cost;
use App\Models\GeneralExpense;
use App\Models\Locality;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class MovementHistoryController extends Controller
{
    public function generatePDF(Request $request)
    {
        $localityId = $request->input('locality_id');
        $locality = Locality::find($localityId);
        if (!$locality) {
            return redirect()->back()->with('error', 'No se encontró la localidad.');
        }
        $now = now();

        $payments = Payment::where('locality_id', $localityId)
            ->where('updated_at', '<=', $now)
            ->whereHas('user', function ($query) use ($localityId) {
                $query->where('locality_id', $localityId)
                      ->role(['supervisor']);
            })
            ->with(['user.locality'])
            ->withTrashed()
            ->orderByDesc('updated_at')
            ->get();

        $debts = Debt::where('locality_id', $localityId)
            ->where('updated_at', '<=', $now)
            ->whereHas('user', function ($query) use ($localityId) {
                $query->where('locality_id', $localityId)
                      ->role(['supervisor']);
            })
            ->with(['user.locality'])
            ->withTrashed()
            ->orderByDesc('updated_at')
            ->get();

        $costs = Cost::where('locality_id', $localityId)
            ->where('updated_at', '<=', $now)
            ->whereHas('user', function ($query) use ($localityId) {
                $query->where('locality_id', $localityId)
                      ->role(['supervisor']);
            })
            ->with(['user.locality'])
            ->withTrashed()
            ->orderByDesc('updated_at')
            ->get();

        $generalExpenses = GeneralExpense::where('locality_id', $localityId)
            ->where('updated_at', '<=', $now)
            ->whereHas('user', function ($query) use ($localityId) {
                $query->where('locality_id', $localityId)
                      ->role(['supervisor']);
            })
            ->with(['user.locality'])
            ->withTrashed()
            ->orderByDesc('updated_at')
            ->get();

        $modules = [
            'Payments'        => $payments,
            'Debts'           => $debts,
            'Costs'           => $costs,
            'GeneralExpenses' => $generalExpenses,
        ];

        $weekDays = [
            'Monday'    => 'Lunes',
            'Tuesday'   => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday'  => 'Jueves',
            'Friday'    => 'Viernes',
            'Saturday'  => 'Sábado',
            'Sunday'    => 'Domingo',
        ];

        $moduleNames = [
            'Payments'        => 'Pagos',
            'Debts'           => 'Deudas',
            'Costs'           => 'Costos',
            'GeneralExpenses' => 'Gastos Generales',
        ];

        $groupedByDay = [];
        foreach ($modules as $moduleName => $movements) {
            foreach ($movements as $movement) {
                $date = Carbon::parse($movement->updated_at)->startOfDay();
                $groupedByDay[$date->format('Y-m-d')][] = [
                    'movement' => $movement,
                    'module'   => $moduleNames[$moduleName] ?? $moduleName,
                ];
            }
        }

        krsort($groupedByDay);

        $formattedGroupedByDay = [];
        foreach ($groupedByDay as $dateKey => $entries) {
            $date = Carbon::createFromFormat('Y-m-d', $dateKey);
            $dayEs = $weekDays[$date->format('l')] ?? $date->format('l');
            $formattedGroupedByDay[$dayEs . ', ' . $date->format('d/m/Y')] = $entries;
        }

        $pdf = Pdf::loadView('reports.pdfMovementsHistory', [
            'groupedByDay'      => $formattedGroupedByDay,
            'authUserLocality'  => $locality,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('Historial_Movimientos_' . $locality->name . '_' . Carbon::now()->format('d_m_Y') . '.pdf');
    }
}
