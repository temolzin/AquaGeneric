<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Locality;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\InventoryCategory;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $user = Auth::user();
        $userLocalityId = $user->locality_id;

        $components = Inventory::with(['locality', 'creator', 'category'])
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('material', 'like', "%{$search}%")
                    ->orWhereHas('locality', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('creator', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('category', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->when($userLocalityId, function ($query) use ($userLocalityId) {
                return $query->where('locality_id', $userLocalityId);
            })
            ->whereNull('deleted_at')
            ->paginate(10);

        $localities = Locality::select('id', 'name')->get();
        $users = User::select('id', 'name')->get();
        $categories = InventoryCategory::where('locality_id', $userLocalityId)
                ->orWhereNull('locality_id')
                ->orderByRaw('locality_id IS NULL DESC')
                ->orderBy('created_at', 'desc')
                ->get();

        return view('inventory.index', compact('components', 'localities', 'users', 'categories'));
    }

    public function create()
    {
        $user = Auth::user();
        $localities = Locality::select('id', 'name')->get();
        $users = User::select('id', 'name')->get();
        $categories = InventoryCategory::where('locality_id', $user->locality_id)->get();
        
        return view('inventory.create', compact('localities', 'users', 'user', 'categories'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $request->merge([
            'locality_id' => $user->locality_id,
            'created_by' => $user->id,
        ]);

        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'amount' => 'required|integer|min:0',
            'inventory_category_id' => 'required|exists:inventory_categories,id',
            'material' => 'nullable|string|max:50',
            'dimensions' => 'nullable|string|max:50',
        ]);

        Inventory::create($request->all());

        return redirect()->route('inventory.index')->with('success', 'Componente creado con éxito.');
    }

    public function show($id)
    {
        $component = Inventory::with(['locality', 'creator', 'category'])->findOrFail($id);
        return view('inventory.show', compact('component'));
    }

    public function edit($id)
    {
        $component = Inventory::findOrFail($id);
        $user = Auth::user();
        $localities = Locality::select('id', 'name')->get();
        $users = User::select('id', 'name')->get();
        $categories = InventoryCategory::where('locality_id', $user->locality_id)->get();
        
        return view('inventory.edit', compact('component', 'localities', 'users', 'user', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $request->merge([
            'locality_id' => $user->locality_id,
            'created_by' => $user->id,
        ]);

        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'amount' => 'required|integer|min:0',
            'inventory_category_id' => 'required|exists:inventory_categories,id',
            'material' => 'nullable|string|max:50',
            'dimensions' => 'nullable|string|max:50',
        ]);

        $component = Inventory::findOrFail($id);
        $component->update($request->all());

        return redirect()->route('inventory.index')->with('success', 'Componente actualizado con éxito.');
    }

    public function destroy($id)
    {
        $component = Inventory::findOrFail($id);
        $component->delete();

        return redirect()->route('inventory.index')->with('success', 'Componente eliminado con éxito.');
    }

    public function generateInventoryPdf(Request $request)
    {
        set_time_limit(300);
        $authUser = Auth::user();
        $query = Inventory::with('category')->where('locality_id', $authUser->locality_id);
        
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('material', 'like', "%{$search}%")
                    ->orWhere('id', $search)
                    ->orWhereHas('category', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $components = $query->get();
        $pdf = Pdf::loadView('reports.pdfInventory', compact('components', 'authUser'))
                    ->setPaper('A4', 'portrait');
        return $pdf->stream('inventario.pdf');
    }

    public function downloadTemplate()
    {
        try {
            $user = Auth::user();
            $categories = InventoryCategory::where('locality_id', $user->locality_id)->get();
            
            // Crear contenido CSV
            $csvContent = "Nombre,Cantidad,Categoría,Descripción,Material,Dimensiones\n";
            
            // Agregar ejemplos con categorías reales
            $sampleData = [
                ['Válvula de bola', '50', $categories->first()->name ?? 'Válvulas y Reguladores', 'Válvula para control de flujo', 'PVC', '1 pulgada'],
                ['Tubería acero', '100', $categories->get(1)->name ?? 'Tuberías y Conexiones', 'Tubería para distribución', 'Acero inoxidable', '2 pulgadas'],
                ['Medidor digital', '25', $categories->get(2)->name ?? 'Medidores de Agua', 'Medidor para monitoreo de consumo', 'Latón', '50 mm']
            ];
            
            foreach ($sampleData as $row) {
                $csvContent .= '"' . implode('","', $row) . "\"\n";
            }
            
            // Agregar fila de instrucciones
            $csvContent .= "\n# INSTRUCCIONES:\n";
            $csvContent .= "# - Las columnas Nombre, Cantidad y Categoría son OBLIGATORIAS\n";
            $csvContent .= "# - La Categoría debe coincidir exactamente con una de estas:\n";
            
            foreach ($categories as $category) {
                $csvContent .= "#   * " . $category->name . "\n";
            }
            
            $csvContent .= "# - Cantidad debe ser un número entero\n";
            $csvContent .= "# - Elimine las filas de instrucciones antes de importar\n";
            
            // Configurar headers para descarga
            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="plantilla_inventario.csv"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];
            
            return response($csvContent, 200, $headers);
            
        } catch (\Exception $e) {
            return redirect()->route('inventory.index')
                ->with('error', 'Error al generar plantilla: ' . $e->getMessage());
        }
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        try {
            $user = Auth::user();
            $file = $request->file('csv_file');
            
            $imported = 0;
            $errors = [];
            $skipErrors = $request->has('skip_errors');
            
            if (($handle = fopen($file->getPathname(), 'r')) !== FALSE) {
                // Leer encabezados y mapear a inglés
                $header = fgetcsv($handle);
                $headerMap = [
                    'Nombre' => 'name',
                    'Cantidad' => 'amount', 
                    'Categoría' => 'category_name',
                    'Descripción' => 'description',
                    'Material' => 'material',
                    'Dimensiones' => 'dimensions'
                ];
                
                $englishHeader = [];
                foreach ($header as $spanishCol) {
                    $englishHeader[] = $headerMap[$spanishCol] ?? $spanishCol;
                }
                
                $lineNumber = 1;
                while (($row = fgetcsv($handle)) !== FALSE) {
                    $lineNumber++;
                    
                    try {
                        if (count($row) !== count($englishHeader)) {
                            throw new \Exception("Número de columnas incorrecto");
                        }
                        
                        $data = array_combine($englishHeader, $row);
                        
                        // Validar campos requeridos
                        if (empty($data['name']) || empty($data['amount']) || empty($data['category_name'])) {
                            throw new \Exception("Faltan campos requeridos");
                        }
                        
                        // Buscar categoría por nombre
                        $category = InventoryCategory::where('name', $data['category_name'])
                            ->where('locality_id', $user->locality_id)
                            ->first();
                        
                        if (!$category) {
                            throw new \Exception("Categoría no encontrada: {$data['category_name']}");
                        }
                        
                        // Crear el registro
                        Inventory::create([
                            'locality_id' => $user->locality_id,
                            'created_by' => $user->id,
                            'name' => $data['name'],
                            'description' => $data['description'] ?? null,
                            'amount' => intval($data['amount']),
                            'inventory_category_id' => $category->id,
                            'material' => $data['material'] ?? null,
                            'dimensions' => $data['dimensions'] ?? null,
                        ]);
                        
                        $imported++;
                        
                    } catch (\Exception $e) {
                        $errors[] = "Línea {$lineNumber}: " . $e->getMessage();
                        if (!$skipErrors) {
                            throw new \Exception("Error en línea {$lineNumber}: " . $e->getMessage());
                        }
                    }
                }
                fclose($handle);
            }
            
            $message = "Se importaron {$imported} registros correctamente";
            if (!empty($errors)) {
                $message .= ". Errores: " . implode('; ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= " y " . (count($errors) - 5) . " más...";
                }
            }
            
            return redirect()->route('inventory.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            return redirect()->route('inventory.index')
                ->with('error', 'Error al importar: ' . $e->getMessage());
        }
    }
}
