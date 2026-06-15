<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Debt;
use App\Models\GeneralEarning;
use App\Models\GeneralExpense;
use App\Models\Locality;
use App\Models\Payment;
use App\Models\LocalityNotice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Membership;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Crypt;
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
        $totalUsers = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', User::ROLE_CUSTOMER);
        })->count();
        $totalLocalities = Locality::count();
        $totalMemberships = Membership::count();

        $membershipDistribution = Membership::withCount('localities')
            ->get()
            ->map(function ($membership) {
                return [
                    'name' => $membership->name,
                    'total' => $membership->localities_count
                ];
            });

        $active = 0;
        $expiringSoon = 0;
        $expired = 0;
        $thresholdDays = 30;

        $allLocalities = Locality::all();
        $activeMemberships = 0;
        $expiredMemberships = 0;
        $withoutTokenMemberships = 0;

        foreach ($allLocalities as $locality) {

            if (!$locality->token) {
                $withoutTokenMemberships++;
                continue;
            }
            try {
            $tokenValidation = Crypt::decrypt($locality->token);

            $endDate = Carbon::parse(
                $tokenValidation['data']['endDate']
            );

            $endDate->isFuture()
                ? $activeMemberships++
                : $expiredMemberships++;

            } catch (Exception $e) {
                $expiredMemberships++;
            }
        }
        $membershipStatusChart = [
            'Activas' => $activeMemberships,
            'Caducadas' => $expiredMemberships,
            'Sin Token' => $withoutTokenMemberships,
        ];

        foreach ($allLocalities as $loc) {
        $status = $loc->getSubscriptionStatus();
        ($status === Locality::SUBSCRIPTION_ACTIVE && $loc->token)
            ? (function () use ($loc, $thresholdDays, &$active, &$expiringSoon, &$expired) {
                try {
                    $tokenValidation = Crypt::decrypt($loc->token);
                    $endDate = Carbon::parse($tokenValidation['data']['endDate'])->startOfDay();
                    $today = now()->startOfDay();
                    $diff = $today->diffInDays($endDate, false);

                    $diff >= 0 && $diff <= $thresholdDays
                        ? $expiringSoon++
                        : $active++;
                } catch (\Exception $e) {
                    $expired++;
                }
            })()
            : $expired++;
        }

        $membershipStatusCounts = [
            'active' => $active,
            'expiringSoon' => $expiringSoon,
            'expired' => $expired,
        ];

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

        $monthlyPayments = Payment::selectRaw('SUM(amount) as total, MONTH(created_at) as month')
            ->where('locality_id', $authUser->locality_id)
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyGeneralEarnings = GeneralEarning::selectRaw('SUM(amount) as total, MONTH(earning_date) as month')
            ->where('locality_id', $authUser->locality_id)
            ->whereYear('earning_date', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyExpensesData = GeneralExpense::selectRaw('SUM(amount) as total, MONTH(expense_date) as month')
            ->where('locality_id', $authUser->locality_id)
            ->whereYear('expense_date', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyIncomes = array_fill(1, 12, 0);
        $monthlyExpenses = array_fill(1, 12, 0);
        $monthlyGains = array_fill(1, 12, 0);

        foreach ($monthlyPayments as $payment) {
            $monthlyIncomes[$payment->month] += $payment->total;
        }

        foreach ($monthlyGeneralEarnings as $earning) {
            $monthlyIncomes[$earning->month] += $earning->total;
        }

        foreach ($monthlyExpensesData as $expense) {
            $monthlyExpenses[$expense->month] = $expense->total;
        }

        foreach ($monthlyIncomes as $month => $income) {
            $monthlyGains[$month] = $income - $monthlyExpenses[$month];
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
        $locality = $authUser->locality;
        $remindersSentToday = $locality && $locality->last_reminder_sent_at && $locality->last_reminder_sent_at->isToday();

        $waterConnections = $authUser->customer?->waterConnections ?? collect();
        $totalDebts = $waterConnections->flatMap->debts->count();
        $pendingDebts = $waterConnections->flatMap->debts->where('status', '!=', 'paid')->count();
        $totalOwed = $waterConnections->flatMap->debts->where('status', '!=', 'paid')->sum(function ($debt) {
        return $debt->amount - $debt->debt_current;
        });

        $notices = LocalityNotice::with(['creator', 'locality'])
            ->where('locality_id', $authUser->locality_id)
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [
            'customersByLocality' => $customersByLocality,
            'customersWithDebts' => $customersWithDebts,
            'customersWithoutDebts' => $customersWithoutDebts,
            'noDebtsForCurrentMonth' => $noDebtsForCurrentMonth,
            'months' => $months,
            'monthlyIncomes' => array_values($monthlyIncomes),
            'annualExpenses' => array_values($monthlyExpenses),
            'annualGains' => array_values($monthlyGains),
            'localities' => $localities,
            'paidDebtsExpiringSoon' => $this->getPaidDebtsExpiringSoon($authUser->locality_id),
            'membershipDistribution' => $membershipDistribution,
            'membershipStatusCounts' => $membershipStatusCounts,
        ];

        return view('dashboard', compact(
            'data',
            'authUser',
            'hasMailConfig',
            'waterConnections',
            'totalDebts',
            'pendingDebts',
            'totalOwed',
            'notices',
            'totalUsers',
            'totalLocalities',
            'totalMemberships',
            'membershipDistribution'
            , 'membershipStatusCounts'
        ));
    }

    public function getEarningsByLocality(Request $request)
    {
        $localityId = $request->input('locality_id');

        $monthlyPayments = Payment::selectRaw('SUM(amount) as total, MONTH(created_at) as month')
            ->where('locality_id', $localityId)
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyGeneralEarnings = GeneralEarning::selectRaw('SUM(amount) as total, MONTH(earning_date) as month')
            ->where('locality_id', $localityId)
            ->whereYear('earning_date', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyExpenses = GeneralExpense::selectRaw('SUM(amount) as total, MONTH(expense_date) as month')
            ->where('locality_id', $localityId)
            ->whereYear('expense_date', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $incomes = array_fill(1, 12, 0);
        $expenses = array_fill(1, 12, 0);
        $gains = array_fill(1, 12, 0);

        foreach ($monthlyPayments as $payment) {
            $incomes[$payment->month] += $payment->total;
        }

        foreach ($monthlyGeneralEarnings as $earning) {
            $incomes[$earning->month] += $earning->total;
        }

        foreach ($monthlyExpenses as $expense) {
            $expenses[$expense->month] = $expense->total;
        }

        foreach ($incomes as $month => $income) {
            $gains[$month] = $income - $expenses[$month];
        }

        return response()->json([
            'incomes' => array_values($incomes),
            'expenses' => array_values($expenses),
            'gains' => array_values($gains),
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
        $locality = $authUser->locality;
        $mailConfig = $locality?->mailConfiguration;

        if (!$mailConfig || !$mailConfig->isComplete()) {
            return back()->with('error', 'No hay configuración de correo válida para esta localidad.');
        }


        if ($locality->last_reminder_sent_at && $locality->last_reminder_sent_at->isToday()) {
            return back()->with('warning', 'Ya se enviaron recordatorios el día de hoy.');
        }

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

       $uniqueCustomers = $allCustomers
            ->filter(function ($customer) {
                return !empty($customer['customerEmail']);
            })
            ->unique('customerEmail')
            ->values();

        if ($uniqueCustomers->isEmpty()) {
            return back()->with('warning', 'No hay clientes con correo válido para enviar recordatorios.');
        }

        $jobsDispatched = false;

        $uniqueCustomers->chunk(50)->each(function ($chunk) use ($authUser, &$jobsDispatched) {
            dispatch(new SendUpcomingPaymentEmails($chunk, $authUser->id));
            $jobsDispatched = true;
        });

        if ($jobsDispatched) {
            $locality->last_reminder_sent_at = now();
            $locality->save();
        }

        return back()->with('success', 'Los correos están en proceso de envío y pronto llegarán a sus destinatarios.');
    }
}
