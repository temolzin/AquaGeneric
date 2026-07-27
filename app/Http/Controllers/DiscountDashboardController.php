<?php

namespace App\Http\Controllers;

use App\Models\DiscountHistory;
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

    private function getDiscountsByModule(string $module, int $month, int $year)
    {
        return DiscountHistory::byUserLocality()->join('discounts as d', 'discount_histories.discount_id', '=', 'd.id')->where('discount_histories.module', $module)->whereMonth('discount_histories.created_at', $month)->whereYear('discount_histories.created_at', $year)->select('d.name', DB::raw('COUNT(*) as total'))->groupBy('d.name')->orderByDesc('total')->get();
    }
}
