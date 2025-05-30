<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Debt;

class AdvancePaymentController extends Controller
{
    public function index()
    {
        return view('advancePayments.index');
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

    public function paymentGraphReport(Request $request)
    {
        $authUser = auth()->user();

        $debt = Debt::selectRaw('
            MONTH(start_date) as start_month,
            MONTH(end_date) as end_month,
            YEAR(end_date) as end_year,
            amount
        ')
        ->where('status', 'paid') 
        ->where('start_date', '>', now())
        ->first();

        Carbon::setLocale('es');
        $debt->start_month_name = Carbon::create()->month($debt->start_month)->translatedFormat('F');
        $debt->end_month_name = Carbon::create()->month($debt->end_month)->translatedFormat('F');

        $chartImages = $request->input('charts');

        $pdf = PDF::loadView('reports.advancePaymentGraphReport', compact('authUser', 'chartImages', 'debt'))->setPaper('A4', 'portrait');

        return $pdf->stream('advancePaymentGraphReport.pdf');
    }
}
