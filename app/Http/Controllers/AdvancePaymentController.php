<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Debt;

class AdvancePaymentController extends Controller
{
    public function index()
    {
        $advancePayments = DB::table('debts')
            ->where('status', Debt::STATUS_PAID)
            ->whereColumn('created_at', '<=', DB::raw('DATE_ADD(start_date, INTERVAL 1 MONTH)'))
            ->select(
                DB::raw("YEAR(start_date) as year"),
                DB::raw("MONTH(start_date) as month"),
                DB::raw("SUM(amount) as total")
            )
            ->groupBy(DB::raw("YEAR(start_date), MONTH(start_date)"))
            ->orderBy(DB::raw("YEAR(start_date), MONTH(start_date)"))
            ->get();

        $months = [];
        $totals = [];

        foreach ($advancePayments as $payment)
        {
            $monthName = Carbon::create()
                ->month($payment->month)
                ->locale('es')
                ->translatedFormat('F');

            $months[] = $monthName . ' ' . $payment->year;
            $totals[] = $payment->total;
        }

        return view('advancePayments.index', compact('months', 'totals'));
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
        ->where('start_date', '>', now())
        ->first();

        Carbon::setLocale('es');
        $debt->startMonthName = Carbon::create()->month($debt->start_month)->translatedFormat('F');
        $debt->endMonthName = Carbon::create()->month($debt->end_month)->translatedFormat('F');

        $chartImages = $request->input('charts');

        $pdf = PDF::loadView('reports.advancePaymentGraphReport', compact('authUser', 'chartImages', 'debt'))->setPaper('A4', 'portrait');

        return $pdf->stream('advancePaymentGraphReport.pdf');
    }
}
