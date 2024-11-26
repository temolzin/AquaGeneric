<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GeneralExpense;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class GeneralExpenseController extends Controller
{
    public function index(Request $request)
    {
        $authUser = auth()->user();
        $query = GeneralExpense::where('general_expenses.locality_id', $authUser->locality_id)
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
        return view('generalExpenses.index', compact('expenses'));
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
        $expense->type = $request->input('typeUpdate');
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
            $expenses = GeneralExpense::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('locality_id', $authUser->locality_id)
            ->sum('amount');

            $monthlyExpenses[$month] = $expenses;
            $totalExpenses += $expenses;
        }

        $pdf = PDF::loadView('reports.annualExpenses', compact('monthlyExpenses', 'totalExpenses', 'year', 'authUser'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('annual_expenses_' . $year . '.pdf');
    }
}
