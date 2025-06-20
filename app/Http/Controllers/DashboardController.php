<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Debt;
use App\Models\Locality;
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
        $localities = Locality::all();

        $customersByLocality = Customer::where('locality_id', $authUser->locality_id)->count();

        $customersWithDebts = Customer::where('locality_id', $authUser->locality_id)
        ->whereHas('waterConnections.debts', function ($query) {
            $query->where('status', '!=', 'paid');
        })
        ->count();

        $monthlyEarnings = Payment::selectRaw('SUM(amount) as total, MONTH(created_at) as month')
        ->where('locality_id', $authUser->locality_id)
        ->whereYear('created_at', Carbon::now()->year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $earningsPerMonth = array_fill(1, 12, 0);

        foreach ($monthlyEarnings as $earning) {
            $earningsPerMonth[$earning->month] = $earning->total;
        }

        $customersWithoutDebts = Customer::where('locality_id', $authUser->locality_id)->whereDoesntHave('waterConnections.debts', function ($query) {
            $query->where('status', '!=', 'paid');
        })->count();

        $currentMonth = Carbon::now();
        $debtsThisMonth = Debt::whereYear('start_date', $currentMonth->year)
            ->whereMonth('start_date', $currentMonth->month)
            ->count();

        $noDebtsForCurrentMonth = ($debtsThisMonth === 0);

        $months = collect(range(1, 12))->map(function ($month) {
            return ucfirst(Carbon::create()->month($month)->locale('es')->monthName);
        });

        $data = [
            'customersByLocality' => $customersByLocality,
            'customersWithDebts' => $customersWithDebts,
            'customersWithoutDebts' => $customersWithoutDebts,
            'noDebtsForCurrentMonth' => $noDebtsForCurrentMonth,
            'months' => $months,
            'earningsPerMonth' => array_values($earningsPerMonth),
            'localities' => $localities,
            'paidDebtsExpiringSoon' => $this->getPaidDebtsExpiringSoon($authUser->locality_id),
        ];

        return view('dashboard', compact('data', 'authUser'));
    }

    public function getEarningsByLocality(Request $request)
    {
        $localityId = $request->input('locality_id');

        $monthlyEarnings = Payment::selectRaw('SUM(amount) as total, MONTH(created_at) as month')
        ->where('locality_id', $localityId)
        ->whereYear('created_at', Carbon::now()->year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $earningsPerMonth = array_fill(1, 12, 0);

        foreach ($monthlyEarnings as $earning) {
            $earningsPerMonth[$earning->month] = $earning->total;
        }

        return response()->json([
            'earningsPerMonth' => array_values($earningsPerMonth),
            'months' => collect(range(1, 12))->map(function ($month) {
                return ucfirst(Carbon::create()->month($month)->locale('es')->monthName);
            })
        ]);
    }

    public function getPaidDebtsExpiringSoon($localityId)
    {
        $today = Carbon::today();
        $limit = $today->copy()->addDays(Debt::DASHBOARD_EXPIRING_DAYS);

        $result = [];

        $debts = Debt::with(['waterConnection:id,name,customer_id', 'waterConnection.customer'])
            ->whereHas('waterConnection.customer', fn($q) => $q->where('locality_id', $localityId))
            ->where('status', Debt::STATUS_PAID)
            ->whereBetween('end_date', [$today, $limit])
            ->orderBy('end_date')
            ->get();

        foreach ($debts as $debt) {
            $customer = $debt->waterConnection->customer;

            $result[] = [
                'customerId' => $customer->id,
                'customerName' => "{$customer->name} {$customer->last_name}",
                'customerPhoto' => $customer?->getFirstMediaUrl('customerGallery') ?: asset('img/userDefault.png'),
                'waterConnectionName' => $debt->waterConnection->name,
                'endDate' => Carbon::parse($debt->end_date)->format('d/m/Y'),
                'daysRemaining' => $today->diffInDays(Carbon::parse($debt->end_date)->endOfDay()) + 1,
            ];
        }
        
        return $result;
    }
}
