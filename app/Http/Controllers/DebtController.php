<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Debt;
use Illuminate\Support\Facades\DB;

class DebtController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $customers = Customer::all();

        $debts = Debt::with('customer')
            ->whereHas('customer', function ($query) use ($search) {
                $query->where('id', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(name, ' ', last_name) LIKE ?", ["%{$search}%"]);
            })
            ->where('status', '!=', 'paid')
            ->select('customer_id')
            ->groupBy('customer_id')
            ->selectRaw('SUM(amount) as total_amount')
            ->paginate(10);

        return view('debts.index', compact('debts', 'customers'));
    }




    public function store(Request $request)
    {
        $startMonth = $request->input('start_date');
        $endMonth = $request->input('end_date');

        $startDate = new \DateTime($startMonth . '-01');
        $endDate = (new \DateTime($endMonth . '-01'))->modify('last day of this month');

        $existingDebt = Debt::where('customer_id', $request->input('customer_id'))
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where('start_date', '<=', $endDate->format('Y-m-d'))
                    ->where('end_date', '>=', $startDate->format('Y-m-d'));
            })
            ->exists();

        if ($existingDebt) {
            return redirect()->back()->with('error', 'El Usuario ya tiene una deuda en este rango de fechas.')->withInput();
        }

        Debt::create([
            'customer_id' => $request->input('customer_id'),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'amount' => $request->input('amount'),
            'note' => $request->input('note'),
        ]);

        return redirect()->route('debts.index')->with('success', 'Deuda creada exitosamente.');
    }

    public function assignAll(Request $request)
    {
        $customers = Customer::with('cost')->get();

        $startMonth = $request->input('start_date');
        $endMonth = $request->input('end_date');

        $startDate = new \DateTime($startMonth . '-01');
        $endDate = (new \DateTime($endMonth . '-01'))->modify('first day of next month')->modify('-1 day');

        $note = $request->note ?? 'Deuda asignada manualmente';

        $allHaveDebt = true;

        foreach ($customers as $customer) {
            $cost = $customer->cost;

            if (!$cost || !$cost->price) {
                continue;
            }

            $existingDebt = Debt::where('customer_id', $customer->id)
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->where('start_date', '<=', $endDate->format('Y-m-d'))
                        ->where('end_date', '>=', $startDate->format('Y-m-d'));
                })
                ->exists();

            if ($existingDebt) {
                continue;
            }

            $allHaveDebt = false;
            Debt::create([
                'customer_id' => $customer->id,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'amount' => $cost->price,
                'note' => $note,
            ]);
        }

        if ($allHaveDebt) {
            return redirect()->back()->with('error', 'Ya todos los usuarios tienen la deuda del periodo.');
        }

        return redirect()->back()->with('success', 'Deudas asignadas a todos los Usuarios.');
    }


    public function destroy($id, Request $request)
    {
        $debt = Debt::find($id);

        if (!$debt) {
            return redirect()->back()->with('error', 'Deuda no encontrada.');
        }

        $debt->delete();

        return redirect()->back()->with('success', 'Deuda eliminada con éxito.')
            ->with('modal_id', $request->input('modal_id'));
    }
}
