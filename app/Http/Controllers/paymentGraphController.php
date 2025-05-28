<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Payment;
use App\Models\Debt;
use Carbon\Carbon;

class paymentGraphController extends Controller
{
    public function index(Request $request)
    {
        
        $authUser = Auth::user();

        $query = Payment::with('debt.customer')
            ->orderBy('id', 'desc')
            ->where('locality_id', $authUser->locality_id);

        if ($request->filled('name')) {
            $query->whereHas('debt.customer', function ($q) use ($request) {
                $q->whereRaw("CONCAT(customers.name, ' ', customers.last_name) LIKE ?", ['%' . $request->name . '%'])
                ->orWhereRaw("CONCAT(customers.last_name, ' ', customers.name) LIKE ?", ['%' . $request->name . '%']);
            });
        }

        $advancePayments = Payment::selectRaw('
                YEAR(debts.start_date) as year,
                MONTH(debts.start_date) as month,
                SUM(payments.amount) as total_amount
            ')
            ->join('debts', 'payments.debt_id', '=', 'debts.id')
            ->where('payments.locality_id', $authUser->locality_id)
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $advancePayments->transform(function ($item) {
            $item->month_name = ucfirst(Carbon::create($item->year, $item->month, 1)->locale('es')->monthName);
            return $item;
        });

        $months = collect(range(1, 12))->map(function ($m) {
            return ucfirst(Carbon::create(2025, $m, 1)->locale('es')->monthName);
        })->toArray();

        $advancePaymentsRaw = Payment::selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->where('locality_id', $authUser->locality_id)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $advancePayments = [];
        foreach (range(1, 12) as $monthNum) {
            $advancePayments[] = $advancePaymentsRaw[$monthNum] ?? 0;
        }

        $earningsPerMonth = Payment::selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->where('locality_id', $authUser->locality_id)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $earningsPerMonth = collect(range(1, 12))->mapWithKeys(function ($month) use ($earningsPerMonth) {
            return [$month => $earningsPerMonth[$month] ?? 0];
        })->values()->toArray();

        return view('advancePayment.index', [
            'advancePayments' => $advancePayments,
            'months' => $months,
            'earningsPerMonth' => $earningsPerMonth,
        ]);
    }

    public function saveCharts(Request $request)
    {
       $request->validate([
            'charts' => 'required|array',
            'charts.*' => 'required|string',
        ]);

        $chartNames = [];
        foreach ($request->input('charts') as $index => $chartData) {
            $imageData = explode(',', $chartData)[1];
            $imageName = 'chart-' . now()->format('YmdHis') . '-' . uniqid() . "-{$index}.png";
            $imagePath = 'reports/img/' . $imageName;
            
            Storage::disk('public')->put($imagePath, base64_decode($imageData));
            $chartNames[] = $imageName;
        }

        session(['chartImages' => $chartNames]);

        return response()->json([
            'images' => array_map(function($name) {
                return asset('storage/reports/img/' . $name);
            }, $chartNames),
        ]);
    }

    public function paymentGraphPdf()
    {
        $authUser = Auth::user();

        $today = Carbon::now();
        $currentMonth = $today->month;
        $currentYear = $today->year;

        $debt = Debt::selectRaw('
            MONTH(start_date) as start_month,
            MONTH(end_date) as end_month,
            YEAR(end_date) as end_year,
            CASE 
                WHEN debt_current IS NOT NULL THEN debt_current
                ELSE amount
            END as amount_due,
            CASE 
                WHEN status = "paid" OR status = "parcial" THEN "Paid"
                ELSE "Unpaid"
            END as payment_status
        ')
        ->where(function ($query) use ($currentMonth) {
            $query->whereMonth('start_date', '<=', $currentMonth)
                ->whereMonth('end_date', '>=', $currentMonth)
                ->orWhere(function ($q) use ($currentMonth) {
                    $q->whereMonth('start_date', '>', $currentMonth)
                        ->whereRaw('status IN ("paid", "parcial")');
                });
        })
        ->whereYear('end_date', $currentYear)
        ->limit(1) 
        ->first(); 

        if (!$debt) {
            $debt = (object) [
                'start_month_name' => 'N/A',
                'end_month_name' => 'N/A',
                'end_year' => 'N/A',
                'amount_due' => 0.00,
                'payment_status' => 'Unpaid',
            ];
        } else {
            Carbon::setLocale('es');

            $debt->start_month_name = Carbon::createFromDate(null, $debt->start_month, 1)->translatedFormat('F');
            $debt->end_month_name = Carbon::createFromDate(null, $debt->end_month, 1)->translatedFormat('F');
        }

        $imageNames = session('chartImages', []);
        $imagePaths = [];

        foreach ($imageNames as $imageName) {           
            $imagePaths[] = storage_path('app/public/reports/img/' . $imageName);
        }

        $pdf = PDF::loadView('reports.paymentGraph', [
            'authUser' => $authUser,
            'imagePaths' => $imagePaths,
            'debt' => $debt,
        ])->setPaper('A4', 'portrait');

        return $pdf->stream('paymentGraph.pdf');
    }
}
