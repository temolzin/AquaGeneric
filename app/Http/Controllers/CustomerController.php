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
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendCustomerCredentialsEmail;

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
            $query->where(function($q) use ($search) {
                $q->whereRaw("CONCAT(name, ' ', last_name) LIKE ?", ["%{$search}%"])
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('id', 'LIKE', "%{$search}%");
            });
        }

        $customers = $query->paginate(10);
        return view('customers.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $authUser = auth()->user();

        $emailRule = $request->has('showPassword')
            ? 'required|email|unique:users,email'
            : 'required|email|unique:customers,email';

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
            'email' => $emailRule,
            'marital_status' => 'required|string',
            'status' => 'required|string',
            'responsible_name' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        $customerData = $request->all();
        $customerData['locality_id'] = $authUser->locality_id;
        $customerData['created_by'] = $authUser->id;

        if ($request->has('showPassword')) {

            $passview = $request->password;

            $user = User::create([
                'name' => $request->name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($passview),
                'locality_id' => $authUser->locality_id,
            ]);

            session(['passview_'.$user->id => $passview]);

            $user->assignRole('cliente');

            $customerData['user_id'] = $user->id;
            $customerData['name'] = $user->name;
            $customerData['last_name'] = $user->last_name;
            $customerData['email'] = $user->email;

            $customer = Customer::create($customerData);

            if ($request->hasFile('photo')) {
                $customer->addMediaFromRequest('photo')->toMediaCollection('customerGallery');
            }

            $hash = md5($customer->id);
            $this->generateUserAccessPDF($hash);

            return redirect()->route('customers.index')->with(['success' => 'Cliente registrado correctamente.','pdf_hash' => $hash]);
        }

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
            $name = $request->input('nameUpdate');
            $lastName = $request->input('lastNameUpdate');
            $email = $request->input('emailUpdate');

            $existingCustomer = Customer::where('email', $email)
                ->where('id', '!=', $id)
                ->first();

            if ($existingCustomer) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'El email ya está en uso por otro cliente.');
            }

            if ($customer->user && $email !== $customer->user->email) {
                $existingUser = User::where('email', $email)
                    ->where('id', '!=', $customer->user->id)
                    ->first();

                if ($existingUser) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'El email ya está en uso por otro usuario.');
                }
            }

            if (!$customer->user && $email) {
                $existingUserWithEmail = User::where('email', $email)->first();

                if ($existingUserWithEmail) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'El email ya está registrado como usuario. Asigne este cliente al usuario existente.');
                }
            }

            if ($customer->user && empty($email)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'El email es requerido para clientes con cuenta de usuario.');
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

            $customer->name = $name;
            $customer->last_name = $lastName;
            $customer->email = $email;
            $customer->save();

            if ($customer->user) {
                $customer->user->update([
                    'name' => $name,
                    'last_name' => $lastName,
                    'email' => $email,
                ]);

                if ($customer->locality_id && $customer->locality_id != $customer->user->locality_id) {
                    $customer->user->locality_id = $customer->locality_id;
                    $customer->user->save();
                }
            }

            if ($request->hasFile('photo')) {
                $customer->clearMediaCollection('customerGallery');
                $customer->addMediaFromRequest('photo')->toMediaCollection('customerGallery');
            }

            return redirect()->route('customers.index')->with('success', 'Cliente actualizado correctamente.');
        }

        return redirect()->back()->with('error', 'Cliente no encontrado.');
    }

    public function assignOrUpdatePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|min:6',
        ]);

        $customer = Customer::with('user')->findOrFail($id);

        $passview = $request->password;

        if (!$customer->user) {
            $user = new User();
            $user->name = $customer->name;
            $user->last_name = $customer->last_name;
            $user->email = $customer->email;
            $user->password = Hash::make($passview);
            $user->locality_id = $customer->locality_id;
            $user->save();

            $user->assignRole('cliente');
            $customer->user_id = $user->id;
            $customer->save();

            session(['passview_'.$user->id => $passview]);

            SendCustomerCredentialsEmail::dispatch($customer->id, Auth::id(), $passview);

            $hash = md5($customer->id);
            $pdfUrl = route('generate.user.access.pdf', $hash);

            return redirect()
            ->route('customers.index')
            ->with('success', 'Usuario creado y contraseña asignada correctamente.')
            ->with('pdf_url', route('generate.user.access.pdf', ['hash' => $hash]));
        }

        $customer->user->password = Hash::make($passview);
        $customer->user->save();

        session(['passview_'.$customer->user->id => $passview]);

        SendCustomerCredentialsEmail::dispatch($customer->id, Auth::id(), $passview);

        $hash = md5($customer->id);
        $pdfUrl = route('generate.user.access.pdf', $hash);

        return redirect()
        ->route('customers.index')
        ->with('success', 'Contraseña actualizada correctamente.')
        ->with('pdf_url', route('generate.user.access.pdf', ['hash' => $hash]));
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

    public function generateUserAccessPDF($hash)
    {
        try {
            $customer = Customer::all()->first(function ($c) use ($hash) {
                return md5($c->id) === $hash;
            });

            if (!$customer) {
                abort(404, 'Cliente no encontrado');
            }

            $temporaryPassword = session('passview_'.$customer->user->id) ?? 'Contraseña no disponible';

            $data = [
                'customer' => $customer,
                'user' => $customer->user,
                'temporaryPassword' => $temporaryPassword,
                'authUser' => auth()->user(),
                'date' => now()->format('j \\d\\e F \\d\\e Y'),
                'showCustomerId' => true,
            ];

            $pdf = Pdf::loadView('reports.genneratepasswordforcustomer', $data)
                    ->setPaper('A4', 'portrait');

            return $pdf->stream('DatosUsuario_'.$customer->id.'.pdf');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al generar PDF: ' . $e->getMessage());
        }
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

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="plantilla_clientes.csv"',
    ];

        $content = "\xEF\xBB\xBF";

        $content .= "nombre,apellido,correo_electronico,calle,colonia,localidad,estado,codigo_postal,numero_exterior,numero_interior,estado_civil,estado_titular,nota\n";

        $content .= "Andrea,Estrada,andy@gmail.com,retorno Acolman,Acolman,Acolman,Estado de Mexico,55870,1,2,Casado,Con vida,test\n";
        $content .= "Andres,Rueda,andres@gmail.com,palma,Valle,Valle de Bravo,Valle de Bravo,55879,3,6,Soltero,Fallecido,test\n";

        return response($content, 200, $headers);
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

            $filePath = $file->getPathname();
            $content = file_get_contents($filePath);
            $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);

            $sourceEncoding = ($encoding && $encoding !== 'UTF-8') ? $encoding : 'UTF-8';
            $content = mb_convert_encoding($content, 'UTF-8', $sourceEncoding);
            file_put_contents($filePath, $content);

            $handle = fopen($filePath, 'r');

            $headers = fgetcsv($handle);
            if (isset($headers[0])) {
                $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);
                $headers[0] = preg_replace('/^ï»¿/', '', $headers[0]);
                $headers[0] = ltrim($headers[0], "\0\x0B\xEF\xBB\xBF");
            }

            foreach ($headers as &$header) {
                $header = trim($header);
                $header = preg_replace('/^ï»¿/', '', $header);
            }
            unset($header);

            $expectedHeaders = ['nombre', 'apellido', 'correo_electronico', 'calle', 'colonia', 'localidad', 'estado', 'codigo_postal', 'numero_exterior', 'numero_interior', 'estado_civil', 'estado_titular', 'nota'];

            if (count($headers) !== count($expectedHeaders)) {
                fclose($handle);
                return response()->json([
                    'success' => false,
                    'message' => 'Número de columnas incorrecto. Se esperaban ' . count($expectedHeaders) . ' columnas, se encontraron ' . count($headers),
                    'expected_headers' => $expectedHeaders,
                    'received_headers' => $headers
                ], 400);
            }

            $headersMatch = true;
            $headerMismatches = [];
            foreach ($expectedHeaders as $index => $expectedHeader) {
                if (strtolower(trim($headers[$index] ?? '')) !== strtolower($expectedHeader)) {
                    $headersMatch = false;
                    $headerMismatches[] = "Columna " . ($index + 1) . ": esperado '{$expectedHeader}', recibido '" . ($headers[$index] ?? 'vacío') . "'";
                }
            }

            if (!$headersMatch) {
                fclose($handle);
                return response()->json([
                    'success' => false,
                    'message' => 'Los encabezados no coinciden con la plantilla oficial.',
                    'expected_headers' => $expectedHeaders,
                    'received_headers' => $headers,
                    'mismatches' => $headerMismatches
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
                } else {
                    $errors[] = $result['error'];
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
            foreach ($rowData as &$field) {
                if (is_string($field)) {
                    if (preg_match('/(Ã.)/u', $field)) {
                        $field = utf8_decode($field);
                    }

                    $field = mb_convert_encoding($field, 'UTF-8', 'UTF-8');
                    $field = @iconv('UTF-8', 'UTF-8//TRANSLIT', $field);
                }
            }
            unset($field);

            $requiredFields = [
                'nombre', 'apellido', 'correo_electronico', 'calle', 'colonia',
                'localidad', 'estado', 'codigo_postal', 'numero_exterior'
            ];

            $missingFields = [];
            foreach ($requiredFields as $index => $fieldName) {
                if (empty(trim($rowData[$index] ?? ''))) {
                    $missingFields[] = $fieldName;
                }
            }

            if (!empty($missingFields)) {
                return [
                    'success' => false,
                    'error' => "Fila $rowNumber: Campos requeridos faltantes: " . implode(', ', $missingFields)
                ];
            }

            $zipCode = trim($rowData[7] ?? '');
            if (!is_numeric($zipCode)) {
                return [
                    'success' => false,
                    'error' => "Fila $rowNumber: Código postal '$zipCode' debe ser numérico"
                ];
            }

            $email = trim($rowData[2] ?? '');
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return [
                    'success' => false,
                    'error' => "Fila $rowNumber: Email '$email' no tiene formato válido"
                ];
            }

            $maritalStatusInput = trim($rowData[10] ?? '');
            $maritalStatusMap = ['Soltero' => 0, 'Casado' => 1];

            if (!isset($maritalStatusMap[$maritalStatusInput])) {
                return [
                    'success' => false,
                    'error' => "Fila $rowNumber: Estado civil '$maritalStatusInput' inválido. Solo se permiten: Soltero, Casado"
                ];
            }

            $statusInput = trim($rowData[11] ?? 'Con vida');
            $statusMap = ['Con vida' => 1, 'Fallecido' => 0];

            if (!isset($statusMap[$statusInput])) {
                return [
                    'success' => false,
                    'error' => "Fila $rowNumber: Estado titular '$statusInput' inválido. Solo se permiten: Con vida, Fallecido"
                ];
            }

            $data = [
                'name' => trim($rowData[0]),
                'last_name' => trim($rowData[1]),
                'email' => $email,
                'street' => trim($rowData[3]),
                'block' => trim($rowData[4]),
                'locality' => trim($rowData[5]),
                'state' => trim($rowData[6]),
                'zip_code' => $zipCode,
                'exterior_number' => trim($rowData[8]),
                'interior_number' => trim($rowData[9] ?? ''),
                'marital_status' => $maritalStatusMap[$maritalStatusInput],
                'status' => $statusMap[$statusInput],
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
