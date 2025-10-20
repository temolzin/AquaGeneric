<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Customer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Str;
use App\Models\Debt;
use Illuminate\Support\Facades\Log;
use App\Models\WaterConnection;

class AdvancePaymentController extends Controller
{
    public function index(Request $request)
    {
        $chartData = $this->getChartData();

        return view('advancePayments.index', [
            'payments' => $this->getAdvancePayments($request),
            'customers' => Customer::where('locality_id', auth()->user()->locality_id)->get(),
            'months' => $chartData['months'],
            'totals' => $chartData['totals'],
        ]);
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
    }

    public function show($id)
    {
    }

    public function edit($id)
    {
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
    }

    private function filterByCustomerName($query, string $name)
    {
        $query->whereHas('debt.customer', fn ($q) => $q->where(function ($q) use ($name) {
            $q->whereRaw("CONCAT(name, ' ', last_name) LIKE ?", ["%{$name}%"])
                ->orWhereRaw("CONCAT(last_name, ' ', name) LIKE ?", ["%{$name}%"]);
        }));
    }

    private function filterByPeriod($query, string $period)
    {
        [$monthName, $year] = explode('/', $period);
        $monthNumber = self::MONTHS[strtolower(trim($monthName))] ?? null;

        if ($monthNumber && $year) {
            $query->whereHas('debt', fn ($q) => $q->whereYear('start_date', $year)
                ->whereMonth('start_date', $monthNumber)
                ->orWhere(fn ($q) => $q->whereYear('end_date', $year)
                    ->whereMonth('end_date', $monthNumber)));
        }
    }

    private function getAdvancePayments(Request $request)
    {
        $query = Payment::with(['debt.customer', 'debt.waterConnection'])
            ->whereHas('debt', fn ($q) => $q->where('end_date', '>', now())
                ->where('status', '!=', 'pending'))
            ->where('locality_id', auth()->user()->locality_id)
            ->where('is_future_payment', 1)
            ->latest();

        $request->whenFilled('name', fn () => $this->filterByCustomerName($query, $request->name));
        $request->whenFilled('period', fn () => $this->filterByPeriod($query, $request->period));

        return $query->paginate(10);
    }

    private function getCustomerAdvancePayments(Customer $customer, int $waterConnectionId)
    {
        return Payment::with(['debt.waterConnection'])
            ->whereHas('debt', fn ($q) => $q->where('water_connection_id', $waterConnectionId)
                ->where('end_date', '>', now())
                ->where('status', '!=', Debt::STATUS_PENDING))
            ->whereHas('debt.waterConnection', fn ($q) => $q->where('customer_id', $customer->id))
            ->where('locality_id', auth()->user()->locality_id)
            ->where('is_future_payment', 1)
            ->latest()
            ->get();
    }

    private function generateReportFileName(Customer $customer, $waterConnection): string
    {
        return sprintf(
            'Reporte_Pagos_Adelantados_%s_%s_%s.pdf',
            Str::slug($customer->name),
            Str::slug($waterConnection->name),
            now()->format('Y-m-d')
        );
    }

    public function generateAdvancedPaymentReport(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'water_connection_id' => 'required|exists:water_connections,id',
        ]);

        $customer = Customer::with([
            'waterConnections' => fn ($q) => $q->findOrFail($data['water_connection_id']),
        ])->findOrFail($data['customer_id']);

        $payments = $this->getCustomerAdvancePayments($customer, $data['water_connection_id'])
            ->groupBy('debt_id');

        $totalPayments = $payments->flatten()->sum('amount');

        $pdf = Pdf::loadView('reports.advancedPayments', [
            'customer' => $customer,
            'waterConnection' => $customer->waterConnections->first(),
            'payments' => $payments,
            'totalPayments' => $totalPayments,
            'authUser' => auth()->user(),
        ]);

        $fileName = $this->generateReportFileName($customer, $customer->waterConnections->first());

        return $pdf->stream($fileName);
    }

    public function getCustomersWithAdvancePayments(Request $request)
    {
        $currentMonthStart = Carbon::now()->startOfMonth();

        $paymentsQuery = Payment::with(['customer', 'debt.waterConnection'])
            ->where('locality_id', auth()->user()->locality_id)
            ->where('is_future_payment', 1)
            ->whereHas('debt', fn ($debtQuery) => $debtQuery->where('status', Debt::STATUS_PAID)
                ->where('end_date', '>', $currentMonthStart));

        $customerId = $request->input('customerId');

        if (!$customerId) {
            $customers = $paymentsQuery->get()
                ->pluck('customer')
                ->unique('id')
                ->values();

            return response()->json(['customers' => $customers]);
        }

        $connections = $paymentsQuery->whereHas('customer', fn ($customerQuery) => $customerQuery->where('id', $request->customerId))
            ->get()
            ->pluck('debt.waterConnection')
            ->unique('id')
            ->values();

        return response()->json(['waterConnections' => $connections]);
    }

    public function getAdvanceDebtDates(Request $request)
    {
        $connectionId = $request->input('waterConnectionId');

        $now = Carbon::now()->startOfMonth();

        $debt = DB::table('debts')
            ->join('payments', 'debts.id', '=', 'payments.debt_id')
            ->where('debts.status', 'paid')
            ->where('debts.water_connection_id', $connectionId)
            ->where('debts.end_date', '>', $now)
            ->where('payments.is_future_payment', 1)
            ->orderBy('debts.start_date', 'asc')
            ->select('debts.start_date', 'debts.end_date')
            ->first();

        return $debt
            ? response()->json(['start_date' => $debt->start_date, 'end_date' => $debt->end_date])
            : response();
    }

    public function generatePaymentGraphReport(Request $request)
    {
        $authUser = auth()->user();

        $debt = Debt::selectRaw('
            MONTH(start_date) as start_month,
            MONTH(end_date) as end_month,
            YEAR(end_date) as end_year,
            amount
        ')
            ->where('status', Debt::STATUS_PAID)
            ->where('end_date', '>=', now()->startOfMonth()->addMonth())
            ->first();

        Carbon::setLocale('es');
        if ($debt) {
            $debt->startMonthName = Carbon::create()->month($debt->start_month)->translatedFormat('F');
            $debt->endMonthName = Carbon::create()->month($debt->end_month)->translatedFormat('F');
        }

        $chartImages = json_decode($request->input('charts'), true) ?? [];

        $pdf = Pdf::loadView('reports.advancePaymentGraphReport', compact('authUser', 'chartImages', 'debt'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('advancePaymentGraphReport.pdf');
    }

    private function getChartData()
    {
        $startDate = Carbon::now()->subMonths(3)->startOfMonth();
        $advancePayments = DB::table('payments')
            ->where('is_future_payment', 1)
            ->where('locality_id', auth()->user()->locality_id)
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
            ->orderBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
            ->get();

        $months = [];
        $totals = [];
        $monthMap = [];
        $current = $startDate->copy();
        $endDate = Carbon::now()->startOfMonth();

        while ($current <= $endDate) {
            $year = $current->year;
            $month = $current->month;
            $monthName = $current->locale('es')->translatedFormat('F');
            $monthMap["$year-$month"] = [
                'label' => "$monthName $year",
                'total' => 0,
            ];
            $current->addMonth();
        }

        foreach ($advancePayments as $payment) {
            $monthName = Carbon::create()->month($payment->month)->locale('es')->translatedFormat('F');
            $key = "{$payment->year}-{$payment->month}";
            $monthMap[$key] = [
                'label' => "$monthName {$payment->year}",
                'total' => $payment->total,
            ];
        }

        foreach ($monthMap as $entry) {
            $months[] = $entry['label'];
            $totals[] = $entry['total'];
        }

        return compact('months', 'totals');
    }

    public function generateAdvancedPaymentHistoryReport(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'water_connection_id' => 'required|exists:water_connections,id',
        ]);

        $customer = Customer::findOrFail($request->customer_id);
        $connection = WaterConnection::findOrFail($request->water_connection_id);

        $response = $this->getAdvanceDebtDates(new Request([
            'waterConnectionId' => $connection->id,
        ]));

        $debtDates = json_decode($response->getContent(), true);

        $debt = (object) [
            'startDate' => $debtDates['start_date'] ?? now()->startOfMonth(),
            'endDate' => $debtDates['end_date'] ?? now()->startOfMonth()->addMonths(12),
        ];

        $startDate = Carbon::parse($debt->startDate);
        $endDate = Carbon::parse($debt->endDate);
        $months = [];
        $current = $startDate->copy()->startOfMonth();

        while ($current <= $endDate) {
            $months[] = [
                'label' => $current->isoFormat('MMMM YYYY'),
                'paid' => true,
                'month' => $current->month,
                'year' => $current->year,
            ];
            $current->addMonth();
        }

        $data = [
            'customer' => $customer,
            'connection' => $connection,
            'debt' => $debt,
            'months' => $months,
            'authUser' => auth()->user(),
        ];

        $pdf = Pdf::loadView('reports.advancePaymentsHistory', $data);

        return $pdf->stream('reporte_pago_anticipado_' . $customer->id . '_' . $connection->id . '.pdf');
    }
}
