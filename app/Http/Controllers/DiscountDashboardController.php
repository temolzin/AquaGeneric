<?php

namespace App\Http\Controllers;

use App\Models\DiscountHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DiscountDashboardController extends Controller
{
    public function index()
    {
        $discounts = DiscountHistory::byUserLocality()->join('discounts as d', 'discount_histories.discount_id', '=', 'd.id')->select('d.name',DB::raw('COUNT(*) as total'))->groupBy('d.name')->orderBy('d.name')->get();

        $discountLabels = $discounts->pluck('name');
        $discountData = $discounts->pluck('total');

        $payments = DiscountHistory::byUserLocality()->join('discounts', 'discount_histories.discount_id', '=', 'discounts.id')->where('module', 'payment')->selectRaw('discounts.name, COUNT(*) as total')->groupBy('discounts.name')->orderBy('discounts.name')->get();

        $paymentLabels = $payments->pluck('name');
        $paymentData = $payments->pluck('total');

        $debts = DiscountHistory::byUserLocality()->join('discounts', 'discount_histories.discount_id', '=', 'discounts.id')->where('module', 'debt')->selectRaw('discounts.name, COUNT(*) as total')->groupBy('discounts.name')->orderBy('discounts.name')->get();

        $debtLabels = $debts->pluck('name');
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
        $month = $request->payment_month;
        $year  = $request->payment_year;

        $paymentDiscounts = DiscountHistory::byUserLocality()->join('discounts as d', 'discount_histories.discount_id', '=', 'd.id')->where('module', 'payment')->whereMonth('discount_histories.created_at', $month)->whereYear('discount_histories.created_at', $year)->select('d.name',DB::raw('COUNT(*) as total'))->groupBy('d.name')->orderByDesc('total')->get();

        return response()->json([
            'labels' => $paymentDiscounts->pluck('name'),
            'data'   => $paymentDiscounts->pluck('total')
        ]);
    }
}
