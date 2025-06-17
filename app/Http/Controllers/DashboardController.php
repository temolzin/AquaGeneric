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
            'activePeriods' => $this->getPaidDebtsExpiringSoon($authUser->locality_id),
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
        $thresholdDate = $today->copy()->addDays(20);

        $debtQuery = Debt::whereHas('waterConnection.customer', function ($query) use ($localityId) {
            $query->where('locality_id', $localityId);
        })
            ->where('status', Debt::STATUS_PAID)
            ->whereBetween('end_date', [$today, $thresholdDate])
            ->with([
                'waterConnection:id,name,customer_id',
                'waterConnection.customer'
            ])
            ->orderBy('end_date', 'asc');

        return $debtQuery->get()->map(function ($debt) use ($today) {
            $waterConnection = $debt->waterConnection;
            $customer = $waterConnection->customer ?? null;

            $photoUrl = $customer && $customer->getFirstMediaUrl('customerGallery')
            ? $customer->getFirstMediaUrl('customerGallery')
            : asset('img/userDefault.png');

            $endDate = $debt->end_date ? Carbon::parse($debt->end_date)->format('d/m/Y') : 'Fecha no disponible';
            $endDateCarbon = $debt->end_date ? Carbon::parse($debt->end_date)->endOfDay() : $today;
            $daysRemaining = $today->diffInDays($endDateCarbon);

            return [
                'customerId' => $customer->id ?? null,
                'customerName' => $customer ? "{$customer->name} {$customer->last_name}" : 'Cliente no disponible',
                'customerPhoto' => $photoUrl,
                'waterConnectionName' => $waterConnection->name ?? 'Toma no disponible',
                'endDate' => $endDate,
                'daysRemaining' => $daysRemaining,
            ];
        });
    }
}
