<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $authUser = auth()->user();

        $query = Employee::where('locality_id', $authUser->locality_id)
            ->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $search = $request->input('search');
            
            $query->where(function ($searchQuery) use ($search) {
                $searchQuery->whereRaw("CONCAT(name, ' ', last_name) LIKE ?", ["%{$search}%"])
                    ->orWhere('id', 'LIKE', "%{$search}%");
            });
        }

        $employees = $query->paginate(10);
        return view('employees.index', compact('employees'));
    }

    public function store(Request $request)
    {
        $authUser = auth()->user();

        $validateData = $request->validate([
            'name' => 'required|string',
            'lastName' => 'required|string',
            'locality' => 'required|string',
            'zipCode' => 'required|string',
            'state' => 'required|string',
            'block' => 'required|string',
            'street' => 'required|string',
            'exteriorNumber' => 'required|string',
            'interiorNumber' => 'required|string',
            'email' => 'required|email|unique:employees,email',
            'phoneNumber' => 'nullable|string',
            'salary' => 'required',
            'rol'=>'required|string',
        ]);

        $employeeData = [
            'name'=> $request->name,
            'last_name' => $request -> lastName,
            'locality'=> $request -> locality,
            'zip_code'  => $request -> zipCode,
            'state'  => $request -> state,
            'block'  => $request -> block,
            'street'=> $request -> street,
            'exterior_number'  => $request -> exteriorNumber,
            'interior_number'  => $request -> interiorNumber,
            'email'  => $request -> email,
            'phone_number'  => $request -> phoneNumber,
            'salary'  => $request -> salary,
            'rol'   => $request -> rol,
        ];
        $employeeData['created_by'] = $authUser->id;
        $employeeData['locality_id'] = $authUser->locality_id;

        $employee = Employee::create($employeeData);

        if ($request->hasFile('photo')) {
            $employee->addMediaFromRequest('photo')->toMediaCollection('employeeGallery');
        }

        return redirect()->route('employees.index')->with('success', 'Empleado creado exitosamente.');
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        if ($employee) {
            $employee->name = $request->input('nameUpdate');
            $employee->last_name = $request->input('lastNameUpdate');
            $employee->locality = $request->input('localityUpdate');
            $employee->zip_code = $request->input('zipCodeUpdate');
            $employee->state = $request->input('stateUpdate');
            $employee->block = $request->input('blockUpdate');
            $employee->street = $request->input('streetUpdate');
            $employee->exterior_number = $request->input('exteriorNumberUpdate');
            $employee->interior_number = $request->input('interiorNumberUpdate');
            $employee->email = $request->input('emailUpdate');
            $employee->phone_number = $request->input('phoneNumberUpdate');
            $employee->salary = $request->input('salaryUpdate');
            $employee->rol = $request->input('rolUpdate');

            $employee->save();

            if ($request->hasFile('photo')) {
                $employee->clearMediaCollection('employeeGallery');
                $employee->addMediaFromRequest('photo')->toMediaCollection('employeeGallery');
            }

            return redirect()->route('employees.index')->with('success', 'Empleado actualizado correctamente.');
        }

        return redirect()->back()->with('error', 'Empleado no encontrado.');
    }

    public function show($id)
    {
        $employee = Employee::findOrFail($id);
        return view('employees.show', compact('employee'));
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Empleado eliminado correctamente');
    }

    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        return view('employees.edit', compact('employee'));
    }

    public function generateEmployeeListReport()
    {
        $authUser = auth()->user();
        
        $employees = Employee::where('locality_id', $authUser->locality_id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $pdf = PDF::loadView('reports.generateEmployeeListReport', compact('employees', 'authUser'))
            ->setPaper('A4', 'landscape');

        return $pdf->stream('employees.pdf');
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="plantilla_empleados.csv"',
    ];

        $content = "\xEF\xBB\xBF";

        $content .= "nombre,apellido,localidad,codigo_postal,estado,manzana,calle,numero_exterior,numero_interior,email,telefono,salario,rol\n";

        $content .= "Jorge,Peralta Gonzalez,México,12345,México,5,Avenida Principal,123,99,jorge.peralta@empresa.com,5551234567,15000.00,Administrativo\n";
        $content .= "María,López Hernández,Guadalajara,44100,Jalisco,2,Calle Secundaria,456,7,maria.lopez@empresa.com,3339876543,18000.00,Supervisor\n";

        return response($content, 200, $headers);
    }

    public function import(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'excel_file' => [
                'required',
                'file',
                'mimes:csv,txt',
                'max:10240'
            ]
        ], [
            'excel_file.required' => 'El archivo CSV es requerido.',
            'excel_file.file' => 'El archivo debe ser válido.',
            'excel_file.mimes' => 'Solo se permiten archivos CSV.',
            'excel_file.max' => 'El archivo no debe ser mayor a 10MB.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()->all()
            ], 422);
        }

        try {
            $file = $request->file('excel_file');
            $processed = 0;
            $imported = 0;
            $errors = [];
            $authUser = Auth::user();

            $content = file_get_contents($file->getPathname());
            $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);

            if ($encoding && $encoding !== 'UTF-8') {
                $convertedContent = mb_convert_encoding($content, 'UTF-8', $encoding);
                file_put_contents($file->getPathname(), $convertedContent);
                $content = $convertedContent;
            }

            $handle = fopen($file->getPathname(), 'r');

            if ($handle === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo abrir el archivo CSV.'
                ], 400);
            }

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

            $expectedHeaders = ['nombre', 'apellido', 'localidad', 'codigo_postal', 'estado', 'manzana', 'calle', 'numero_exterior', 'numero_interior', 'email', 'telefono', 'salario', 'rol'];

            if (count($headers) !== count($expectedHeaders)) {
                fclose($handle);
                return response()->json([
                    'success' => false,
                    'message' => 'Formato de archivo incorrecto. El número de columnas no coincide con la plantilla.'
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

                if (empty(array_filter($rowData, function($value) {
                    return $value !== null && $value !== '';
                }))) {
                    continue;
                }

                $result = $this->processEmployeeRowData($rowData, $authUser, $processed);
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

    private function processEmployeeRowData($rowData, $authUser, $rowNumber)
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

            if (count($rowData) < 13) {
                return [
                    'success' => false,
                    'error' => "Fila $rowNumber: Número insuficiente de columnas. Se esperaban 13, se encontraron " . count($rowData)
                ];
            }

            $requiredFields = [
                'nombre' => $rowData[0] ?? '',
                'apellido' => $rowData[1] ?? '',
                'localidad' => $rowData[2] ?? '',
                'codigo_postal' => $rowData[3] ?? '',
                'estado' => $rowData[4] ?? '',
                'calle' => $rowData[6] ?? '',
                'numero_exterior' => $rowData[7] ?? '',
                'email' => $rowData[9] ?? '',
                'telefono' => $rowData[10] ?? '',
                'salario' => $rowData[11] ?? '',
                'rol' => $rowData[12] ?? ''
            ];

            $missingFields = [];
            foreach ($requiredFields as $fieldName => $value) {
                $trimmedValue = trim($value ?? '');
                if (empty($trimmedValue)) {
                    $missingFields[] = $fieldName;
                }
            }

            if (!empty($missingFields)) {
                return [
                    'success' => false,
                    'error' => "Fila $rowNumber: Campos requeridos faltantes: " . implode(', ', $missingFields)
                ];
            }

            $rawEmail = $rowData[9] ?? '';
            $email = trim($rawEmail);
            $email = str_replace(["\xEF\xBB\xBF", "\u{FEFF}"], '', $email);
            $email = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $email);
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            $email = strtolower($email);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return [
                    'success' => false,
                    'error' => "Fila $rowNumber: Email '$email' no tiene formato válido"
                ];
            }

            $phone = trim($rowData[10] ?? '');
            $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
            if (strlen($cleanPhone) < 10 || strlen($cleanPhone) > 15) {
                return [
                    'success' => false,
                    'error' => "Fila $rowNumber: Teléfono '$phone' no tiene formato válido (debe tener entre 10 y 15 dígitos)"
                ];
            }

            $salary = $this->parseSalary($rowData[11] ?? '');
            if ($salary <= 0) {
                return [
                    'success' => false,
                    'error' => "Fila $rowNumber: Salario debe ser mayor a 0"
                ];
            }

            $validRoles = ['Administrativo', 'Supervisor', 'Operativo', 'Gerente'];
            $roleInput = trim($rowData[12] ?? '');
            if (!in_array($roleInput, $validRoles)) {
                return [
                    'success' => false,
                    'error' => "Fila $rowNumber: Rol '$roleInput' inválido. Solo se permiten: " . implode(', ', $validRoles)
                ];
            }

            $zipCode = trim($rowData[3] ?? '');
            if (!preg_match('/^[0-9]{5}$/', $zipCode)) {
                return [
                    'success' => false,
                    'error' => "Fila $rowNumber: Código postal '$zipCode' inválido. Debe tener 5 dígitos"
                ];
            }

            $data = [
                'name' => trim($rowData[0] ?? ''),
                'last_name' => trim($rowData[1] ?? ''),
                'locality' => trim($rowData[2] ?? ''),
                'zip_code' => $zipCode,
                'state' => trim($rowData[4] ?? ''),
                'block' => trim($rowData[5] ?? ''),
                'street' => trim($rowData[6] ?? ''),
                'exterior_number' => trim($rowData[7] ?? ''),
                'interior_number' => trim($rowData[8] ?? ''),
                'email' => $email,
                'phone_number' => $cleanPhone,
                'salary' => $salary,
                'rol' => $roleInput,
                'locality_id' => $authUser->locality_id,
                'created_by' => $authUser->id,
            ];

            if (Employee::where('email', $data['email'])->exists()) {
                return [
                    'success' => false,
                    'error' => "Fila $rowNumber: El email '{$data['email']}' ya existe en el sistema"
                ];
            }

            Employee::create($data);
            return ['success' => true];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => "Fila $rowNumber: Error - " . $e->getMessage()
            ];
        }
    }

    private function parseSalary($salary)
    {
        $salary = trim($salary);
        $salary = str_replace(['$', ',', ' '], '', $salary);
        return floatval($salary);
    }
}
