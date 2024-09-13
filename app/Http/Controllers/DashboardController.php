<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Debt;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        Carbon::setLocale('es');

        $authUser = Auth::user();
        $totalCustomers = Customer::count();

        $customersWithDebts = Customer::whereHas('debts', function ($query) {
            $query->where('status', '!=', 'paid');
        })->count();

        $customersWithoutDebts = Customer::whereDoesntHave('debts', function ($query) {
            $query->where('status', '!=', 'paid');
        })->count();

        $currentMonth = Carbon::now();
        $debtsThisMonth = Debt::whereYear('start_date', $currentMonth->year)
            ->whereMonth('start_date', $currentMonth->month)
            ->count();

        $noDebtsForCurrentMonth = ($debtsThisMonth === 0);

        $monthlyEarnings = Payment::selectRaw('SUM(amount) as total, MONTH(payment_date) as month')
            ->whereYear('payment_date', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $earningsPerMonth = array_fill(1, 12, 0);

        foreach ($monthlyEarnings as $earning) {
            $earningsPerMonth[$earning->month] = $earning->total;
        }

        $months = collect(range(1, 12))->map(function ($month) {
            return ucfirst(Carbon::create()->month($month)->locale('es')->monthName);
        });

        $data = [
            'totalCustomers' => $totalCustomers,
            'customersWithDebts' => $customersWithDebts,
            'customersWithoutDebts' => $customersWithoutDebts,
            'noDebtsForCurrentMonth' => $noDebtsForCurrentMonth,
            'months' => $months,
            'earningsPerMonth' => array_values($earningsPerMonth),
        ];

        return view('dashboard', compact('data', 'authUser'));
    }
}
