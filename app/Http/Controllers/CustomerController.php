<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Cost;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Mpdf\Mpdf;
use App\Models\Debt;
use Illuminate\Support\Facades\Crypt;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $authUser = auth()->user();
        $query = Customer::where('locality_id', $authUser->locality_id)
            ->with('user') 
            ->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('user', function($q) use ($search) {
                $q->whereRaw("CONCAT(name, ' ', last_name) LIKE ?", ["%{$search}%"])
                ->orWhere('email', 'LIKE', "%{$search}%");
            })->orWhere('id', 'LIKE', "%{$search}%");
        }

        $customers = $query->with('user')->paginate(10);
        return view('customers.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $authUser = auth()->user();

        $validatedData = $request->validate([
            'name' => 'required|string',
            'last_name' => 'required|string',
            'street' => 'required|string',
            'block' => 'required|string',
            'locality' => 'required|string',
            'state' => 'required|string',
            'zip_code' => 'required|string',
            'exterior_number' => 'nullable|string',
            'interior_number' => 'required|string',
            'email' => 'required|email|unique:users,email', 
        ]);
        
        $user = User::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => bcrypt('12345'),
            'locality_id' => $authUser->locality_id, 
        ]);

        $user->assignRole('cliente');
        $customerData = $request->all();
        $customerData['locality_id'] = $authUser->locality_id;
        $customerData['created_by'] = $authUser->id;
        $customerData['user_id'] = $user->id;

        $customer = Customer::create($customerData);

        if ($request->hasFile('photo')) {
            $customer->addMediaFromRequest('photo')->toMediaCollection('customerGallery');
        }

        return redirect()->route('customers.index')->with('success', 'Cliente registrado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::with('user')->find($id);
        if ($customer) {
            if ($customer->user) {
                $customer->user->name = $request->input('nameUpdate');
                $customer->user->last_name = $request->input('lastNameUpdate');
                $customer->user->email = $request->input('emailUpdate');
                $customer->user->save();
            }

            $customer->locality = $request->input('localityUpdate');
            $customer->state = $request->input('stateUpdate');
            $customer->zip_code = $request->input('zipCodeUpdate');
            $customer->block = $request->input('blockUpdate');
            $customer->street = $request->input('streetUpdate');
            $customer->exterior_number = $request->input('exteriorNumberUpdate');
            $customer->interior_number = $request->input('interiorNumberUpdate');
            $customer->marital_status = $request->input('maritalStatusUpdate');
            $customer->status = $request->input('statusUpdate');
            $customer->responsible_name = $request->input('responsibleNameUpdate');
            $customer->note = $request->input('noteUpdate');

            $customer->save();

            if ($request->hasFile('photo')) {
                $customer->clearMediaCollection('customerGallery');
                $customer->addMediaFromRequest('photo')->toMediaCollection('customerGallery');
            }

            return redirect()->route('customers.index')->with('success', 'Cliente actualizado correctamente.');
        }

        return redirect()->back()->with('error', 'Cliente no encontrado.');
    }

    public function show($id)
    {
        $customer = Customer::with('user')->findOrFail($id);
        return view('customers.show', compact('customer'));
    }

    public function destroy(Customer $customer)
    {
        if ($customer->user) {
            $customer->user->delete();
        }
        
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Cliente eliminado correctamente.');
    }

    public function pdfCustomers()
    {
        $authUser = auth()->user();
        $customers = Customer::where('locality_id', $authUser->locality_id)
            ->with('user')
            ->get();
        $pdf = PDF::loadView('reports.pdfCustomers', compact('customers', 'authUser'))
            ->setPaper('A4', 'landscape');

        return $pdf->stream('customers.pdf');
    }

    public function reportCurrentCustomers()
    {
        $authUser = auth()->user();
        $customers = Customer::where('locality_id', $authUser->locality_id)
            ->with('user') 
            ->whereDoesntHave('waterConnections.debts', function ($query) {
                $query->where('status', '!=', 'paid');
            })->get();
    
        $pdf = Pdf::loadView('reports.reportCurrentCustomers', compact('customers', 'authUser'));
        return $pdf->stream('reporte_clientes_al_corriente.pdf');
    }

    public function customersWithDebts()
    {
        $authUser = auth()->user();
        $customers = Customer::where('locality_id', $authUser->locality_id)
            ->with('user') 
            ->whereHas('waterConnections.debts', function ($query) {
                $query->where('status', '!=', 'paid');
            })->get();

        $pdf = Pdf::loadView('reports.customersWithDebts', compact('customers', 'authUser'))
            ->setPaper('A4', 'portrait');
        return $pdf->stream('reporte_clientes_con_deudas.pdf');
    }

    public function generatePaymentHistoryReport($debt_id)
    {
        $authUser = auth()->user();
        $debtId = Crypt::decrypt($debt_id);

        $debt = Debt::with(['waterConnection', 'customer.user']) 
            ->findOrFail($debtId);
        $customer = $debt->customer;

        $payments = $debt->payments()
                        ->orderBy('created_at', 'desc')
                        ->get();

        $totalDebt = $debt->amount;
        $totalPayments = $payments->sum('amount');
        $pendingBalance = $totalDebt - $totalPayments;

        $pdf = Pdf::loadView('reports.paymentHistoryReport', compact('debt', 'customer', 'payments', 'totalDebt', 'authUser', 'totalPayments', 'pendingBalance'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('reporte_historial_pagos.pdf');
    }
    
    public function generateCustomerSummaryPdf(Request $request)
    {
        $authUser = auth()->user();
        $query = Customer::where('locality_id', $authUser->locality_id)
            ->with(['waterConnections', 'user']); 

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('user', function($q) use ($search) {
                $q->whereRaw("CONCAT(name, ' ', last_name) LIKE ?", ["%{$search}%"])
                ->orWhere('email', 'LIKE', "%{$search}%");
            })->orWhere('id', 'LIKE', "%{$search}%");
        }   

        $customers = $query->get();
        $pdf = Pdf::loadView('reports.pdfCustomersSummary', compact('customers', 'authUser'))
            ->setPaper('A4', 'landscape');

        return $pdf->stream('customers_summary.pdf');
    }
}
