<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\DiscountHistory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DiscountDashboardController extends Controller
{
    public function index()
    {
        $discounts = DiscountHistory::byUserLocality()->with('discount')->selectRaw('discount_id, COUNT(*) total')->groupBy('discount_id')->get();

        $discountLabels = $discounts->pluck('discount.name');
        $discountData = $discounts->pluck('total');

        $payments = DiscountHistory::byUserLocality()->with('discount')->where('module', 'payment')->selectRaw('discount_id, COUNT(*) total')->groupBy('discount_id')->get();

        $paymentLabels = $payments->pluck('discount.name');
        $paymentData = $payments->pluck('total');

        $debts = DiscountHistory::byUserLocality()->with('discount')->where('module', 'debt')->selectRaw('discount_id, COUNT(*) total')->groupBy('discount_id')->get();

        $debtLabels = $debts->pluck('discount.name');
        $debtData = $debts->pluck('total');

        return view('discountDashboard.index', compact(
            'discountLabels',
            'discountData',
            'paymentLabels',
            'paymentData',
            'debtLabels',
            'debtData'
        ));
    }

    public function showDashboard(request $request)
    {
        $month = $request->payment_month ?? date('n');
        $year  = $request->payment_year ?? date('Y');

        $paymentDiscounts = DB::table('discount_histories as dh')
            ->join('discounts as d', 'd.id', '=', 'dh.discount_id')
            ->select(
                'd.name',
                DB::raw('COUNT(*) as total')
            )
            ->where('dh.module', 'payment')
            ->whereMonth('dh.created_at', $month)
            ->whereYear('dh.created_at', $year)
            ->groupBy('d.id', 'd.name')
            ->orderByDesc('total')
            ->get();

        $paymentLabels = $paymentDiscounts->pluck('name');
        $paymentData   = $paymentDiscounts->pluck('total');

        return view('discounts.dashboard', compact(
            'paymentLabels',
            'paymentData',
            'discountLabels',
            'discountData',
            'debtLabels',
            'debtData',
            'month',
            'year'
        ));
    }

    public function getPaymentChart(request $request)
    {
        $month = $request->payment_month;
        $year  = $request->payment_year;

        $paymentDiscounts = DB::table('discount_histories as dh')->join('discounts as d', 'd.id', '=', 'dh.discount_id')
            ->select(
                'd.name',
                DB::raw('COUNT(*) as total')
            )
            ->where('dh.module', 'payment')
            ->whereMonth('dh.created_at', $month)
            ->whereYear('dh.created_at', $year)
            ->groupBy('d.id', 'd.name')
            ->orderByDesc('total')
            ->get();

        return response()->json([
            'labels' => $paymentDiscounts->pluck('name'),
            'data'   => $paymentDiscounts->pluck('total')
        ]);
    }

    public function getDebtChart(request $request)
    {
        $month = $request->debt_month;
        $year = $request->debt_year;

        $debtDiscounts = DB::table('discount_histories as dh')->join('discounts as d', 'd.id', '=', 'dh.discount_id')
            ->select(
                'd.name',
                DB::raw('COUNT(*) as total')
            )
            ->where('dh.module', 'debt')
            ->whereMonth('dh.created_at', $month)
            ->whereYear('dh.created_at', $year)
            ->groupBy('d.id', 'd.name')
            ->orderByDesc('total')
            ->get();

        return response()->json([
            'labels' => $debtDiscounts->pluck('name'),
            'data'   => $debtDiscounts->pluck('total')
        ]);
    }

    public function generateReport(Request $request)
    {
        $authUser = auth()->user();
        $locality = $authUser->locality;

        $chartImages = json_decode($request->input('charts'), true) ?? [];

        $discountsSummary = Discount::where(function ($q) use ($authUser) {
                if ($authUser && $authUser->locality_id) {
                    $q->where('discounts.locality_id', $authUser->locality_id)
                      ->orWhereNull('discounts.locality_id');
                }
            })
            ->leftJoin('discount_histories as dh', function ($join) {
                $join->on('discounts.id', '=', 'dh.discount_id')
                     ->whereNull('dh.deleted_at');
            })
            ->select(
                'discounts.id',
                'discounts.name',
                'discounts.percentage',
                'discounts.color',
                DB::raw('COUNT(dh.id) as total_uses')
            )
            ->groupBy('discounts.id', 'discounts.name', 'discounts.percentage', 'discounts.color')
            ->orderByDesc('total_uses')
            ->get();

        $monthNames = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        $paymentMonth = $request->input('payment_month');
        $paymentYear  = $request->input('payment_year');
        $paymentMonthLabel = $paymentMonth && isset($monthNames[(int)$paymentMonth]) ? $monthNames[(int)$paymentMonth] : null;

        $paymentQuery = DB::table('discount_histories as dh')
            ->join('discounts as d', 'd.id', '=', 'dh.discount_id')
            ->select(
                'd.id',
                'd.name',
                'd.percentage',
                'd.color',
                DB::raw('COUNT(*) as total')
            )
            ->where('dh.module', 'payment')
            ->whereNull('dh.deleted_at')
            ->where(function ($q) use ($authUser) {
                if ($authUser && $authUser->locality_id) {
                    $q->where('dh.locality_id', $authUser->locality_id)
                      ->orWhereNull('dh.locality_id');
                }
            });

        if ($paymentMonth) {
            $paymentQuery->whereMonth('dh.created_at', $paymentMonth);
        }
        if ($paymentYear) {
            $paymentQuery->whereYear('dh.created_at', $paymentYear);
        }

        $paymentDiscounts = $paymentQuery->groupBy('d.id', 'd.name', 'd.percentage', 'd.color')
            ->orderByDesc('total')
            ->get();

        $debtMonth = $request->input('debt_month');
        $debtYear  = $request->input('debt_year');
        $debtMonthLabel = $debtMonth && isset($monthNames[(int)$debtMonth]) ? $monthNames[(int)$debtMonth] : null;

        $debtQuery = DB::table('discount_histories as dh')
            ->join('discounts as d', 'd.id', '=', 'dh.discount_id')
            ->select(
                'd.id',
                'd.name',
                'd.percentage',
                'd.color',
                DB::raw('COUNT(*) as total')
            )
            ->where('dh.module', 'debt')
            ->whereNull('dh.deleted_at')
            ->where(function ($q) use ($authUser) {
                if ($authUser && $authUser->locality_id) {
                    $q->where('dh.locality_id', $authUser->locality_id)
                      ->orWhereNull('dh.locality_id');
                }
            });

        if ($debtMonth) {
            $debtQuery->whereMonth('dh.created_at', $debtMonth);
        }
        if ($debtYear) {
            $debtQuery->whereYear('dh.created_at', $debtYear);
        }

        $debtDiscounts = $debtQuery->groupBy('d.id', 'd.name', 'd.percentage', 'd.color')
            ->orderByDesc('total')
            ->get();

        $pdf = Pdf::loadView('reports.discountDashboardReport', compact(
            'authUser',
            'locality',
            'chartImages',
            'discountsSummary',
            'paymentDiscounts',
            'debtDiscounts',
            'paymentMonthLabel',
            'paymentYear',
            'debtMonthLabel',
            'debtYear'
        ))->setPaper('A4', 'portrait');

        return $pdf->stream('Reporte_Panel_Descuentos.pdf');
    }
}
