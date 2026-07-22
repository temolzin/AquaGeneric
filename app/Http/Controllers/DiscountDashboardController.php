<?php

namespace App\Http\Controllers;

use App\Models\DiscountHistory;
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
}
