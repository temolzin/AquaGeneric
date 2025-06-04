<?php

namespace App\Http\Controllers;

use App\Models\{Payment, Customer};
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Str;

class AdvancePaymentController extends Controller
{
    public function index(Request $request)
    {
        return view('advancePayments.index', [
            'payments' => $this->getAdvancePayments($request),
            'customers' => Customer::where('locality_id', auth()->user()->locality_id)->get(),
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

        $pdf = Pdf::loadView('reports.advancedPayments', [
            'customer' => $customer,
            'waterConnection' => $customer->waterConnections->first(),
            'payments' => $payments,
            'totalPayments' => $payments->flatten()->sum('amount'),
            'authUser' => auth()->user(),
        ]);

        return $pdf->stream($this->generateReportFilename($customer, $customer->waterConnections->first()));
    }

    private function getAdvancePayments(Request $request)
    {
        $query = Payment::with(['debt.customer', 'debt.waterConnection'])
            ->whereHas('debt', fn ($q) => $q->where('end_date', '>', now())->where('status', '!=', 'pending'))
            ->where('locality_id', auth()->user()->locality_id)
            ->latest();

        $request->whenFilled('name', fn () => $this->filterByCustomerName($query, $request->name));
        $request->whenFilled('period', fn () => $this->filterByPeriod($query, $request->period));

        return $query->paginate(10);
    }

    private function getCustomerAdvancePayments(Customer $customer, int $waterConnectionId)
    {
        return Payment::whereHas('debt', fn ($q) => $q->where('customer_id', $customer->id)
            ->where('water_connection_id', $waterConnectionId)
            ->where('end_date', '>', now())
            ->where('status', '!=', 'pending'))
            ->where('locality_id', auth()->user()->locality_id)
            ->with(['debt'])
            ->latest()
            ->get();
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
                    ->whereMonth('end_date', $monthNumber))
            );
        }
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
}
