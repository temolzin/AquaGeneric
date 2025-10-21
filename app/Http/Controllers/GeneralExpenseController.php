<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GeneralExpense;
use App\Models\ExpenseType;
use App\Models\Payment;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class GeneralExpenseController extends Controller
{
    public function index(Request $request)
    {
        $authUser = auth()->user();
        $query = GeneralExpense::where('general_expenses.locality_id', $authUser->locality_id)
            ->with('expenseType') 
            ->orderBy('general_expenses.created_at', 'desc')
            ->select('general_expenses.*');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('general_expenses.concept', 'LIKE', "%{$search}%")
                ->orWhere('general_expenses.id', 'LIKE', "%{$search}%");
            });
        }

        $expenses = $query->paginate(10);
        
        $expenseTypes = ExpenseType::where('locality_id', $authUser->locality_id)
            ->orderBy('name')
            ->get();

        return view('generalExpenses.index', compact('expenses', 'expenseTypes'));
    }

    public function store(Request $request)
    {
        $authUser = auth()->user();

        $generalExpenseData = $request->all();

        $generalExpenseData['expense_date'] = $request->input('expenseDate');
        $generalExpenseData['locality_id'] = $authUser->locality_id;
        $generalExpenseData['created_by'] = $authUser->id;

        $expense = GeneralExpense::create($generalExpenseData);

        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $expense->addMedia($file)->toMediaCollection('expenseGallery');
        }

        return redirect()->route('generalExpenses.index')->with('success', 'Gasto registrado correctamente.');
    }

    public function show($id)
    {
        $expenses = GeneralExpense::findOrFail($id);
        return view('generalExpenses.show', compact('expenses'));
    }

    public function update(Request $request, $id)
    {
        $expense = GeneralExpense::find($id);

        if (!$expense) {
            return redirect()->back()->with('error', 'Gasto no encontrado.');
        }

        $expense->concept = $request->input('conceptUpdate');
        $expense->description = $request->input('descriptionUpdate');
        $expense->amount = $request->input('amountUpdate');
        $expense->expense_type_id = $request->input('expense_type_id_update');
        $expense->expense_date = $request->input('expenseDateUpdate');

        if ($request->hasFile('receiptUpdate')) {
            $expense->clearMediaCollection('expenseGallery');
            $expense->addMedia($request->file('receiptUpdate'))->toMediaCollection('expenseGallery');
        }

        $expense->save();

        return redirect()->route('generalExpenses.index')->with('success', 'Gasto actualizado correctamente.');
    }

    public function destroy($id)
    {
        $expense = GeneralExpense::find($id);
        $expense->delete();
        return redirect()->route('generalExpenses.index')->with('success', 'Gasto eliminado correctamente.');
    }

    public function weeklyExpensesReport(Request $request)
    {
        $authUser = auth()->user();
        $startDate = Carbon::parse($request->input('weekStartDate'));
        $endDate = Carbon::parse($request->input('weekEndDate'));

        $weeks = [];
        $currentStart = $startDate->copy();
        $totalPeriodExpenses = 0;

        while ($currentStart->lte($endDate)) {
            $currentEnd = $currentStart->copy()->endOfWeek();
            if ($currentEnd->gt($endDate)) {
                $currentEnd = $endDate;
            }

            $dailyExpenses = [];

            $day = $currentStart->copy();
            while ($day->lte($currentEnd)) {
                if ($day->between($startDate, $endDate)) {
                    $expenses = GeneralExpense::where('locality_id', $authUser->locality_id)
                        ->whereDate('expense_date', $day->toDateString())
                        ->sum('amount');
                } else {
                    $expenses = 'N/A';
                }

                $dailyExpenses[$day->format('l')] = $expenses;
                $day->addDay();
            }

            $totalPeriodExpenses += array_sum($dailyExpenses);

            $weeks[] = [
                'start' => $currentStart->toDateString(),
                'end' => $currentEnd->toDateString(),
                'dailyExpenses' => $dailyExpenses,
            ];

            $currentStart = $currentEnd->copy()->addDay();
        }

        $pdf = PDF::loadView('reports.weeklyExpenses', compact('authUser', 'weeks', 'totalPeriodExpenses'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('weekly_expenses_' . now()->format('Ymd') . '.pdf');
    }

    public function annualExpensesReport($year)
    {
        $authUser = auth()->user();
        $year = intval($year);

        $monthlyExpenses = [];
        $totalExpenses = 0;

        for ($month = 1; $month <= 12; $month++) {
            $expenses = GeneralExpense::whereYear('expense_date', $year)
            ->whereMonth('expense_date', $month)
            ->where('locality_id', $authUser->locality_id)
            ->sum('amount');

            $monthlyExpenses[$month] = $expenses;
            $totalExpenses += $expenses;
        }

        $pdf = PDF::loadView('reports.annualExpenses', compact('monthlyExpenses', 'totalExpenses', 'year', 'authUser'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('annual_expenses_' . $year . '.pdf');
    }

    public function weeklyGainsReport(Request $request)
    {
        $authUser = auth()->user();
        $startDate = Carbon::parse($request->input('weekStartDate'));
        $endDate = Carbon::parse($request->input('weekEndDate'));

        $weeks = [];
        $currentStart = $startDate->copy();
        $totalPeriodGains = 0;

        while ($currentStart->lte($endDate)) {
            $currentEnd = $currentStart->copy()->endOfWeek();
            if ($currentEnd->gt($endDate)) {
                $currentEnd = $endDate;
            }

            $dailyGains = [];

            $day = $currentStart->copy();
            while ($day->lte($currentEnd)) {
                if ($day->between($startDate, $endDate)) {
                    $expenses = GeneralExpense::where('locality_id', $authUser->locality_id)
                        ->whereDate('expense_date', $day->toDateString())
                        ->sum('amount');
                    $earnings = Payment::where('locality_id', $authUser->locality_id)
                        ->whereDate('created_at', $day->toDateString())
                        ->sum('amount');
                    $gains = $earnings - $expenses;
                } else {
                    $gains = 'N/A';
                }

                $dailyGains[$day->format('l')] = $gains;
                $day->addDay();
            }

            $totalPeriodGains += array_sum($dailyGains);

            $weeks[] = [
                'start' => $currentStart->toDateString(),
                'end' => $currentEnd->toDateString(),
                'dailyGains' => $dailyGains,
            ];

            $currentStart = $currentEnd->copy()->addDay();
        }

        $pdf = PDF::loadView('reports.weeklyGains', compact('authUser', 'weeks', 'totalPeriodGains'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('weekly_gains_' . now()->format('Ymd') . '.pdf');
    }

    public function annualGainsReport($yearGains)
    {
        $authUser = auth()->user();
        $yearGains = intval($yearGains);

        $monthlyGains = [];
        $totalEarnings = 0;
        $totalExpenses = 0;
        $totalGains = 0;

        for ($month = 1; $month <= 12; $month++) {
            $expenses = GeneralExpense::whereYear('expense_date', $yearGains)
            ->whereMonth('expense_date', $month)
            ->where('locality_id', $authUser->locality_id)
            ->sum('amount');

            $earnings = Payment::whereYear('created_at', $yearGains)
            ->whereMonth('created_at', $month)
            ->where('locality_id', $authUser->locality_id)
            ->sum('amount');

            $gains = $earnings - $expenses;

            $monthlyEarnings[$month] = $earnings;
            $monthlyExpenses[$month] = $expenses;
            $monthlyGains[$month] = $gains;

            $totalEarnings += $earnings;
            $totalExpenses += $expenses;
            $totalGains += $gains;
        }

        $pdf = PDF::loadView('reports.annualGains', compact('monthlyEarnings', 'monthlyExpenses', 'monthlyGains', 'totalEarnings', 'totalExpenses', 'totalGains', 'yearGains', 'authUser'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('annual_gains_' . $yearGains . '.pdf');
    }
}
