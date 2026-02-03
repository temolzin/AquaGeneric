<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Locality;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\InventoryCategory;
use App\Models\LogInventory;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $user = Auth::user();
        $userLocalityId = $user->locality_id;

        $components = Inventory::with([
            'locality',
            'creator',
            'category',
            'logs' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'logs.creator'
        ])
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('material', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%")
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
    
    public function updateAmount(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required|exists:inventory,id',
            'amount' => 'required|integer|min:0',
            'description' => 'required|string|max:500'
        ]);

        $authUser = auth()->user();
        $inventory = Inventory::findOrFail($request->inventory_id);

        if ($inventory->locality_id != $authUser->locality_id) {
            return redirect()->back()->with('error', 'No tienes permisos para actualizar este inventario');
        }

        $previousAmount = $inventory->amount;

        $difference = $request->amount - $previousAmount;

        LogInventory::create([
            'inventory_id' => $inventory->id,
            'locality_id' => $authUser->locality_id,
            'created_by' => $authUser->id,
            'previous_amount' => $previousAmount,
            'amount' => $request->amount,
            'description' => $request->description
        ]);

        $inventory->amount = $request->amount;
        $inventory->save();

        return redirect()->back()->with('success', 'Cantidad actualizada correctamente');
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="plantilla_inventario.csv"',
    ];

        $content = "\xEF\xBB\xBF";

        $content .= "nombre,descripcion_inventario,cantidad,categoria_inventario,descripcion_categoria,material,dimensiones\n";

        $content .= "Tubería PVC 2\",Tubería para agua potable de 2 pulgadas,50,Tuberías y Conexiones,Tuberías y accesorios para conducción de agua,Plástico,2 pulgadas\n";
        $content .= "Válvula de bola,Válvula de paso completo de latón,20,Válvulas y Reguladores,Válvulas para control de flujo,Latón,1/2 pulgada\n";
        $content .= "Cloro granulado,Producto para tratamiento de agua,100,Productos Químicos,Productos químicos para potabilización,Cloro,25 kg por bolsa\n";
        $content .= "Bomba de agua,Bomba centrífuga de acero inoxidable,5,Bombas y Motores,Bombas y motores para sistemas hidráulicos,Acero inoxidable,2 HP de potencia\n";
        $content .= "Filtro de arena,Filtro para tratamiento de agua,8,Filtros y Purificación,Filtros para purificación de agua,Arena sílica,12 pulgadas de diámetro\n";

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
    
            $content = file_get_contents($file->getPathname());
            $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);

            if ($encoding !== 'UTF-8') {
                $convertedContent = mb_convert_encoding($content, 'UTF-8', $encoding);
                file_put_contents($file->getPathname(), $convertedContent);
            }
    
            $handle = fopen($file->getPathname(), 'r');
    
            $headers = fgetcsv($handle);
            $expectedHeaders = ['nombre', 'descripcion_inventario', 'cantidad', 'categoria_inventario', 'descripcion_categoria', 'material', 'dimensiones'];
    
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
            $requiredFields = [
                0 => 'nombre',
                1 => 'descripcion_inventario',
                2 => 'cantidad',
                3 => 'categoria_inventario',
                4 => 'descripcion_categoria',
                5 => 'material',
                6 => 'dimensiones'
            ];

            $missingFields = [];
            foreach ($requiredFields as $index => $fieldName) {
                if (!isset($rowData[$index]) || trim($rowData[$index]) === '') {
                    $missingFields[] = $fieldName;
                }
            }

            if (!empty($missingFields)) {
                return [
                    'success' => false,
                    'error' => "Fila $rowNumber: Campos requeridos faltantes: " . implode(', ', $missingFields)
                ];
            }

            $nombre = trim($rowData[0]);
            $descripcionInventario = trim($rowData[1]);
            $cantidad = trim($rowData[2]);
            $categoriaNombre = trim($rowData[3]);
            $descripcionCategoria = trim($rowData[4]);
            $material = trim($rowData[5]);
            $dimensiones = trim($rowData[6]);

            if (!is_numeric($cantidad) || $cantidad < 0 || floor($cantidad) != $cantidad) {
                return ['success' => false, 'error' => "Fila $rowNumber: Cantidad '$cantidad' debe ser un número entero positivo o cero"];
            }

            $categoria = InventoryCategory::where(function($query) use ($authUser, $categoriaNombre) {
                    $query->where('locality_id', $authUser->locality_id)
                        ->orWhereNull('locality_id');
                })
                ->whereRaw('LOWER(name) = ?', [strtolower($categoriaNombre)])
                ->first();

            if (!$categoria) {
                $categoriaColor = color(rand(1, 15));
                $categoria = InventoryCategory::create([
                    'name' => $categoriaNombre,
                    'description' => $descripcionCategoria ?: 'Sin descripción',
                    'locality_id' => $authUser->locality_id,
                    'created_by' => $authUser->id,
                    'color' => color('random')
                ]);
            } else {
                if (empty($categoria->description) && $descripcionCategoria !== '') {
                    $categoria->description = $descripcionCategoria;
                    $categoria->save();
                }
            }

            $data = [
                'name' => $nombre,
                'description' => $descripcionInventario,
                'amount' => (int) $cantidad,
                'inventory_category_id' => $categoria->id,
                'material' => $material,
                'dimensions' => $dimensiones,
                'locality_id' => $authUser->locality_id,
                'created_by' => $authUser->id,
            ];

            Inventory::create($data);
            return ['success' => true];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => "Fila $rowNumber: Error - " . $e->getMessage()
            ];
        }
    }
}
