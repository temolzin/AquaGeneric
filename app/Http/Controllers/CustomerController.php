<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Cost;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Mpdf\Mpdf;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $authUser = auth()->user();
        $query = Customer::where('locality_id', $authUser->locality_id)->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereRaw("CONCAT(name, ' ', last_name) LIKE ?", ["%{$search}%"]);
        }

        $customers = $query->paginate(10);
        return view('customers.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $authUser = auth()->user();

        $customerData = $request->all();
        $customerData['locality_id'] = $authUser->locality_id;
        $customerData['created_by'] = $authUser->id;

        $customer = Customer::create($customerData);

        if ($request->hasFile('photo')) {
            $customer->addMediaFromRequest('photo')->toMediaCollection('customerGallery');
        }

        return redirect()->route('customers.index')->with('success', 'Usuario registrado correctamente.');
    }

    public function update(Request $request, $id)
    {

        $customer = Customer::find($id);
        if ($customer) {
            $customer->name = $request->input('nameUpdate');
            $customer->last_name = $request->input('lastNameUpdate');
            $customer->block = $request->input('blockUpdate');
            $customer->street = $request->input('streetUpdate');
            $customer->interior_number = $request->input('interiorNumberUpdate');
            $customer->marital_status = $request->input('maritalStatusUpdate');
            $customer->status = $request->input('statusUpdate');
            $customer->responsible_name = $request->input('responsibleNameUpdate');

            $customer->save();

            if ($request->hasFile('photo')) {
                $customer->clearMediaCollection('customerGallery');
                $customer->addMediaFromRequest('photo')->toMediaCollection('customerGallery');
            }


            return redirect()->route('customers.index')->with('success', 'Usuario actualizado correctamente.');
        }

        return redirect()->back()->with('error', 'Usuario no encontrado.');
    }

    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        return view('customers.show', compact('customer'));
    }


    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Usuario eliminado correctamente.');
    }

    public function pdfCustomers()
    {
        $authUser = auth()->user();
        $customers = Customer::where('locality_id', $authUser->locality_id)->get();
        $pdf = PDF::loadView('reports.pdfCustomers', compact('customers', 'authUser'))
            ->setPaper('A4', 'landscape');

        return $pdf->stream('customers.pdf');
    }

    public function reportCurrentCustomers()
    {
        $authUser = auth()->user();
        $customers = Customer::where('locality_id', $authUser->locality_id)
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
            ->whereHas('waterConnections.debts', function ($query) {
            $query->where('status', '!=', 'paid');
        })->get();

        $pdf = Pdf::loadView('reports.customersWithDebts', compact('customers', 'authUser'))
        ->setPaper('A4', 'portrait');
        return $pdf->stream('reporte_clientes_con_deudas.pdf');
    }
}
