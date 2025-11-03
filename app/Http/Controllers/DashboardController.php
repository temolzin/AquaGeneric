<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Debt;
use App\Models\Locality;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use Illuminate\Pagination\LengthAwarePaginator;
use Exception;
use App\Jobs\SendUpcomingPaymentEmails;

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

        $customersWithoutDebts = Customer::where('locality_id', $authUser->locality_id)
            ->whereDoesntHave('waterConnections.debts', function ($query) {
                $query->where('status', '!=', 'paid');
            })->count();

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

        $currentMonth = Carbon::now();
        $debtsThisMonth = Debt::whereYear('start_date', $currentMonth->year)
            ->whereMonth('start_date', $currentMonth->month)
            ->count();

        $noDebtsForCurrentMonth = ($debtsThisMonth === 0);

        $months = collect(range(1, 12))->map(function ($month) {
            return ucfirst(Carbon::create()->month($month)->locale('es')->monthName);
        });

        $mailConfig = $authUser->locality?->mailConfiguration;
        $hasMailConfig = $mailConfig && $mailConfig->isComplete();

        $waterConnections = $authUser->customer?->waterConnections ?? collect();
        $totalDebts = $waterConnections->flatMap->debts->count();
        $pendingDebts = $waterConnections->flatMap->debts->where('status', '!=', 'paid')->count();
        $totalOwed = $waterConnections->flatMap->debts->where('status', '!=', 'paid')->sum(function ($debt) {
        return $debt->amount - $debt->debt_current;
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

        return view('dashboard', compact(
            'data',
            'authUser',
            'hasMailConfig',
            'waterConnections',
            'totalDebts',
            'pendingDebts',
            'totalOwed',
            'notices'
        ));
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

    public function getPaidDebtsExpiringSoon($localityId, $perPage = 5)
    {
        $today = Carbon::today();
        $limit = $today->copy()->addDays(Debt::DASHBOARD_EXPIRING_DAYS);

        $debts = Debt::with(['waterConnection:id,name,customer_id', 'waterConnection.customer'])
            ->whereHas('waterConnection.customer', fn($q) => $q->where('locality_id', $localityId))
            ->where('status', Debt::STATUS_PAID)
            ->whereBetween('end_date', [$today, $limit])
            ->orderBy('end_date')
            ->paginate($perPage);

        $debts->getCollection()->transform(function ($debt) use ($today) {
            $customer = $debt->waterConnection->customer;

            return [
                'customerId' => $customer->id,
                'customerName' => "{$customer->name} {$customer->last_name}",
                'customerPhoto' => $customer?->getFirstMediaUrl('customerGallery') ?: asset('img/userDefault.png'),
                'waterConnectionName' => $debt->waterConnection->name,
                'endDate' => Carbon::parse($debt->end_date)->format('d/m/Y'),
                'daysRemaining' => $today->diffInDays(Carbon::parse($debt->end_date)->endOfDay()) + 1,
                'customerEmail' => $customer->email
            ];
        });

        return $debts;
    }

    public function sendEmailsForDebtsExpiringSoon()
    {
        $authUser = Auth::user();
        $mailConfig = $authUser->locality->mailConfiguration;

        Config::set('mail.mailers.smtp.host', $mailConfig->host);
        Config::set('mail.mailers.smtp.port', $mailConfig->port);
        Config::set('mail.mailers.smtp.username', $mailConfig->username);
        Config::set('mail.mailers.smtp.password', $mailConfig->password);
        Config::set('mail.mailers.smtp.encryption', $mailConfig->encryption);
        Config::set('mail.from.address', $mailConfig->username);
        Config::set('mail.from.name', $mailConfig->from_name ?? config('app.name'));

        $perPage = 5;
        $currentPage = 1;
        $allCustomers = collect();

        do {
            LengthAwarePaginator::currentPageResolver(function () use ($currentPage) {
                return $currentPage;
            });

            $customers = $this->getPaidDebtsExpiringSoon($authUser->locality_id, $perPage);
            $allCustomers = $allCustomers->merge($customers->items());
            $currentPage++;

        } while ($customers->hasMorePages());

        $uniqueCustomers = $allCustomers->unique('customerEmail');

        $uniqueCustomers->chunk(50)->each(function ($chunk) use ($authUser) {
            dispatch(new SendUpcomingPaymentEmails($chunk, $authUser->id));
        });

        return back()->with('success', 'Los correos están en proceso de envío y pronto llegarán a sus destinatarios.');
    }
}
