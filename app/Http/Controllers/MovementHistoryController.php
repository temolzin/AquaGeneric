<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Debt;
use App\Models\Cost;
use App\Models\GeneralExpense;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class MovementHistoryController extends Controller
{
    public function generatePDF(Request $request)
    {
        $localityId = $request->input('locality_id');

        $payments = Payment::where('locality_id', $localityId)
            ->whereHas('user', fn($query) => $query->role(['supervisor', 'secretaria']))
            ->with(['user.locality'])
            ->get();

        $debts = Debt::where('locality_id', $localityId)
            ->whereHas('user', fn($query) => $query->role(['supervisor', 'secretaria']))
            ->with(['user.locality'])
            ->get();

        $costs = Cost::where('locality_id', $localityId)
            ->whereHas('user', fn($query) => $query->role(['supervisor', 'secretaria']))
            ->with(['user.locality'])
            ->get();

        $generalExpenses = GeneralExpense::where('locality_id', $localityId)
            ->whereHas('user', fn($query) => $query->role(['supervisor', 'secretaria']))
            ->with(['user.locality'])
            ->get();

        $modules = [
            'Payments' => $payments,
            'Debts' => $debts,
            'Costs' => $costs,
            'GeneralExpenses' => $generalExpenses,
        ];

        $diasSemana = [
            'Monday'    => 'Lunes',
            'Tuesday'   => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday'  => 'Jueves',
            'Friday'    => 'Viernes',
            'Saturday'  => 'Sábado',
            'Sunday'    => 'Domingo',
        ];

        $modulosEsp = [
            'Payments'        => 'Pagos',
            'Debts'           => 'Deudas',
            'Costs'           => 'Costos',
            'GeneralExpenses' => 'Gastos Generales',
        ];

        $groupedByDay = [];
        foreach ($modules as $moduleName => $movements) {
            foreach ($movements as $movement) {
                $fecha = Carbon::parse($movement->updated_at);
                $diaEn = $fecha->format('l');
                $diaEs = $diasSemana[$diaEn] ?? $diaEn;
                $diaCompleto = $diaEs . ', ' . $fecha->format('d/m/Y');

                $groupedByDay[$diaCompleto][] = [
                    'movement' => $movement,
                    'module'   => $modulosEsp[$moduleName] ?? $moduleName,
                ];
            }
        }

        $locality = $payments->first()->locality
            ?? $debts->first()->locality
            ?? $costs->first()->locality
            ?? $generalExpenses->first()->locality
            ?? null;

        $pdf = Pdf::loadView('reports.pdfMovementsHistory', [
            'locality'      => $locality,
            'groupedByDay'  => $groupedByDay,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('Historial_de_Movimientos_' . Carbon::now()->format('d_m_Y') . '.pdf');
    }
}
