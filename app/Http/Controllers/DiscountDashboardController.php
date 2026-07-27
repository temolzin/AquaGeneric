<?php

namespace App\Http\Controllers;

use App\Models\DiscountHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DiscountDashboardController extends Controller
{
    public function index()
    {
        $discounts = DB::table('discount_histories as dh')->join('discounts as d', 'dh.discount_id', '=', 'd.id')->select('d.name',DB::raw('COUNT(*) as total'))->groupBy('d.name')->orderBy('d.name')->get();

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

    public function showDashboard(request $request)
    {
        $month = $request->payment_month ?? date('n');
        $year  = $request->payment_year ?? date('Y');

        $paymentDiscounts = DB::table('discount_histories as dh')->join('discounts as d', 'd.id', '=', 'dh.discount_id')->select('d.name',DB::raw('COUNT(*) as total'))->where('dh.module', 'payment')->whereMonth('dh.created_at', $month)->whereYear('dh.created_at', $year)->groupBy('d.id', 'd.name')->orderByDesc('total')->get();

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

        $paymentDiscounts = DB::table('discount_histories as dh')->join('discounts as d', 'd.id', '=', 'dh.discount_id')->select('d.name',DB::raw('COUNT(*) as total'))->where('dh.module', 'payment')->whereMonth('dh.created_at', $month)->whereYear('dh.created_at', $year)->groupBy('d.name')->orderByDesc('total')->get();

        return response()->json([
            'labels' => $paymentDiscounts->pluck('name'),
            'data'   => $paymentDiscounts->pluck('total')
        ]);
    }

    public function getDebtChart(request $request)
    {
        $month = $request->debt_month;
        $year = $request->debt_year;

        $debtDiscounts = DB::table('discount_histories as dh')->join('discounts as d', 'd.id', '=', 'dh.discount_id')->select('d.name',DB::raw('COUNT(*) as total'))->where('dh.module', 'debt')->whereMonth('dh.created_at', $month)->whereYear('dh.created_at', $year)->groupBy('d.name')->orderByDesc('total')->get();

        return response()->json([
            'labels' => $debtDiscounts->pluck('name'),
            'data'   => $debtDiscounts->pluck('total')
        ]);
    }
}
