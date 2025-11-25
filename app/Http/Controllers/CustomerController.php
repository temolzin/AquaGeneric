<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Cost;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Mpdf\Mpdf;
use App\Models\Debt;
use Illuminate\Support\Facades\Crypt;
use PhpOffice\PhpSpreadsheet\IOFactory; 
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $authUser = auth()->user();
        $query = Customer::where('locality_id', $authUser->locality_id)->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereRaw("CONCAT(name, ' ', last_name) LIKE ?", ["%{$search}%"])
                ->orWhere('id', 'LIKE', "%{$search}%");
        }

        $customers = $query->paginate(10);
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
        ]);

        $customerData = $request->all();
        $customerData['locality_id'] = $authUser->locality_id;
        $customerData['created_by'] = $authUser->id;

        $customer = Customer::create($customerData);

        if ($request->hasFile('photo')) {
            $customer->addMediaFromRequest('photo')->toMediaCollection('customerGallery');
        }

        return redirect()->route('customers.index')->with('success', 'Cliente registrado correctamente.');
    }

    public function update(Request $request, $id)
    {

        $customer = Customer::find($id);
        if ($customer) {
            $customer->name = $request->input('nameUpdate');
            $customer->last_name = $request->input('lastNameUpdate');
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
            $customer->email = $request->input('emailUpdate');

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
        $customer = Customer::findOrFail($id);
        return view('customers.show', compact('customer'));
    }


    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Cliente eliminado correctamente.');
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

    public function generatePaymentHistoryReport($debt_id)
    {
        $authUser = auth()->user();
        $debtId = Crypt::decrypt($debt_id);

        $debt = Debt::with(['waterConnection', 'customer'])->findOrFail($debtId);
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
            ->with('waterConnections');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereRaw("CONCAT(name, ' ', last_name) LIKE ?", ["%{$search}%"])
                ->orWhere('id', 'LIKE', "%{$search}%");
        }   

        $customers = $query->get();
        $pdf = Pdf::loadView('reports.pdfCustomersSummary', compact('customers', 'authUser'))
            ->setPaper('A4', 'landscape');

        return $pdf->stream('customers_summary.pdf');
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:csv|max:10240' 
        ]);

        try {
            $file = $request->file('excel_file');
            $processed = 0;
            $imported = 0;
            $errors = [];
            $authUser = Auth::user();
            
            $handle = fopen($file->getPathname(), 'r');
            
            $headers = fgetcsv($handle);
            $expectedHeaders = ['nombre', 'apellido', 'correo_electronico', 'calle', 'colonia', 'localidad', 'estado', 'codigo_postal', 'numero_exterior', 'numero_interior', 'estado_civil', 'estado_titular', 'nota'];
            
            if ($headers !== $expectedHeaders) {
                fclose($handle);
                return response()->json([
                    'success' => false,
                    'message' => 'Formato de archivo incorrecto. Por favor descarga la plantilla oficial.'
                ], 400);
            }
            
            while (($rowData = fgetcsv($handle)) !== FALSE) {
                $processed++;
                
                if (empty(array_filter($rowData))) {
                    continue;
                }
                
                $result = $this->processRowData($rowData, $authUser, $processed);
                if ($result['success']) {
                    $imported++;
                }
            }
            
            fclose($handle);
            
            return response()->json([
                'success' => true,
                'message' => 'Importación completada',
                'processed' => $processed,
                'imported' => $imported,
                'failed' => count($errors),
                'errors' => $errors
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el archivo CSV: ' . $e->getMessage()
            ], 500);
        }
    }

    private function processRowData($rowData, $authUser, $rowNumber)
    {
        try {
            $requiredFields = [
                'nombre' => $rowData[0] ?? '',
                'apellido' => $rowData[1] ?? '',
                'correo_electronico' => $rowData[2] ?? '',
                'calle' => $rowData[3] ?? '',
                'colonia' => $rowData[4] ?? '',
                'localidad' => $rowData[5] ?? '',
                'estado' => $rowData[6] ?? '',
                'codigo_postal' => $rowData[7] ?? '',
                'numero_exterior' => $rowData[8] ?? ''
            ];
            
            $missingFields = [];
            foreach ($requiredFields as $fieldName => $value) {
                if (empty(trim($value ?? ''))) {
                    $missingFields[] = $fieldName;
                }
            }
            
            if (!empty($missingFields)) {
                return [
                    'success' => false,
                    'error' => "Fila $rowNumber: Campos requeridos faltantes: " . implode(', ', $missingFields)
                ];
            }
            
            $email = trim($rowData[2] ?? '');
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return [
                    'success' => false,
                    'error' => "Fila $rowNumber: Email '$email' no tiene formato válido"
                ];
            }
            
            $maritalStatusInput = strtolower(trim($rowData[10] ?? ''));
            $validMaritalStatus = ['casado', 'soltero'];
            
            if (!in_array($maritalStatusInput, $validMaritalStatus)) {
                return [
                    'success' => false,
                    'error' => "Fila $rowNumber: Estado civil '$maritalStatusInput' inválido. Solo se permiten: " . implode(', ', $validMaritalStatus)
                ];
            }
            
            $statusInput = strtolower(trim($rowData[11] ?? 'con vida'));
            $validStatus = ['con vida', 'fallecido'];
            
            if (!in_array($statusInput, $validStatus)) {
                return [
                    'success' => false,
                    'error' => "Fila $rowNumber: Estado titular '$statusInput' inválido. Solo se permiten: " . implode(', ', $validStatus)
                ];
            }
            
            $maritalStatus = 0; 
            if ($maritalStatusInput === 'soltero') {
                $maritalStatus = 0;
            } elseif ($maritalStatusInput === 'casado') {
                $maritalStatus = 1;
            }
            
            $status = 0;
            if ($statusInput === 'con vida') {
                $status = 1;
            } elseif ($statusInput === 'fallecido') {
                $status = 0;
            }
            
            $data = [
                'name' => trim($rowData[0] ?? ''),
                'last_name' => trim($rowData[1] ?? ''),
                'email' => $email,
                'street' => trim($rowData[3] ?? ''),
                'block' => trim($rowData[4] ?? ''),
                'locality' => trim($rowData[5] ?? ''),
                'state' => trim($rowData[6] ?? ''),
                'zip_code' => $zipCode,
                'exterior_number' => trim($rowData[8] ?? ''),
                'interior_number' => trim($rowData[9] ?? ''),
                'marital_status' => $maritalStatus,
                'status' => $status,
                'note' => trim($rowData[12] ?? ''),
                'locality_id' => $authUser->locality_id,
                'created_by' => $authUser->id,
            ];
            
            if (Customer::where('email', $data['email'])->exists()) {
                return [
                    'success' => false,
                    'error' => "Fila $rowNumber: El email '{$data['email']}' ya existe en el sistema"
                ];
            }
            
            Customer::create($data);
            return ['success' => true];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => "Fila $rowNumber: Error - " . $e->getMessage()
            ];
        }
    }
}
