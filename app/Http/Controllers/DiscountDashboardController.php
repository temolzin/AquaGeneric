<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\DiscountHistory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DiscountDashboardController extends Controller
{
    public function index(Request $request)
    {
        $selectedMonth = (int) $request->input('month', now()->month);
        $selectedYear = (int) $request->input('year', now()->year);

        $discounts = DiscountHistory::byUserLocality()->join('discounts as d', 'discount_histories.discount_id', '=', 'd.id')->select('d.name',DB::raw('COUNT(*) as total'))->groupBy('d.name')->orderBy('d.name')->get();

        $discountLabels = $discounts->pluck('name');
        $discountData = $discounts->pluck('total');

        $payments = $this->getDiscountsByModule('payment', $selectedMonth, $selectedYear);

        $paymentLabels = $payments->pluck('name');
        $paymentData = $payments->pluck('total');

        $debts = $this->getDiscountsByModule('debt', $selectedMonth, $selectedYear);

        $debtLabels = $debts->pluck('name');
        $debtData = $debts->pluck('total');

        return view('discountDashboard.index', compact(
            'discountLabels',
            'discountData',
            'paymentLabels',
            'paymentData',
            'debtLabels',
            'debtData',
            'selectedMonth',
            'selectedYear'
        ));
    }

    public function showDashboard(Request $request)
    {
        $month = $request->payment_month ?? date('n');
        $year  = $request->payment_year ?? date('Y');

        $paymentDiscounts = DiscountHistory::byUserLocality()->join('discounts as d', 'discount_histories.discount_id', '=', 'd.id')->where('module', 'payment')->whereMonth('discount_histories.created_at', $month)->whereYear('discount_histories.created_at', $year)->select('d.name',DB::raw('COUNT(*) as total'))->groupBy('d.name')->orderByDesc('total')->get();

        $paymentLabels = $paymentDiscounts->pluck('name');
        $paymentData = $paymentDiscounts->pluck('total');

        return view('discountDashboard.index', compact(
            'paymentLabels',
            'paymentData',
            'month',
            'year'
        ));
    }

    public function getPaymentChart(Request $request)
    {
        $month = (int) ($request->payment_month ?: now()->month);
        $year  = (int) ($request->payment_year ?: now()->year);
        $paymentDiscounts = $this->getDiscountsByModule('payment', $month, $year);

        return response()->json([
            'labels' => $paymentDiscounts->pluck('name'),
            'data'   => $paymentDiscounts->pluck('total')
        ]);
    }

    public function getDebtChart(Request $request)
    {
        $month = (int) ($request->debt_month ?: now()->month);
        $year  = (int) ($request->debt_year ?: now()->year);
        $debtDiscounts = $this->getDiscountsByModule('debt', $month, $year);

        return response()->json([
            'labels' => $debtDiscounts->pluck('name'),
            'data' => $debtDiscounts->pluck('total'),
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

        $itemsFirstPage = 8;
        $itemsNextPages = 20;
        $firstPageSummary = $discountsSummary->take($itemsFirstPage);
        $otherPagesSummary = $discountsSummary->slice($itemsFirstPage)->chunk($itemsNextPages);
        $pagesSummary = 1 + (int) ceil(max(0, $discountsSummary->count() - $itemsFirstPage) / $itemsNextPages);
        $firstPagePayments = $paymentDiscounts->take($itemsFirstPage);
        $otherPagesPayments = $paymentDiscounts->slice($itemsFirstPage)->chunk($itemsNextPages);
        $pagesPayments = 1 + (int) ceil(max(0, $paymentDiscounts->count() - $itemsFirstPage) / $itemsNextPages);
        $firstPageDebts = $debtDiscounts->take($itemsFirstPage);
        $otherPagesDebts = $debtDiscounts->slice($itemsFirstPage)->chunk($itemsNextPages);
        $pagesDebts = 1 + (int) ceil(max(0, $debtDiscounts->count() - $itemsFirstPage) / $itemsNextPages);
        $totalPages = $pagesSummary + $pagesPayments + $pagesDebts;

        $pdf = Pdf::loadView('reports.discountDashboardReport', compact(
            'authUser',
            'locality',
            'chartImages',
            'firstPageSummary',
            'otherPagesSummary',
            'firstPagePayments',
            'otherPagesPayments',
            'firstPageDebts',
            'otherPagesDebts',
            'totalPages',
            'paymentMonthLabel',
            'paymentYear',
            'debtMonthLabel',
            'debtYear'
        ))->setPaper('A4', 'portrait');

        return $pdf->stream('Reporte_Panel_Descuentos.pdf');
    } 
  
    private function getDiscountsByModule(string $module, int $month, int $year)
    {
        return DiscountHistory::byUserLocality()->join('discounts as d', 'discount_histories.discount_id', '=', 'd.id')->where('discount_histories.module', $module)->whereMonth('discount_histories.created_at', $month)->whereYear('discount_histories.created_at', $year)->select('d.name', DB::raw('COUNT(*) as total'))->groupBy('d.name')->orderByDesc('total')->get();
    }
} 
