<?php

namespace App\Http\Controllers;

use App\Models\WaterConnection;
use App\Models\Customer;
use App\Models\Cost;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Crypt;

class WaterConnectionController extends Controller
{
    public function index(Request $request)
    {
        $authUser = auth()->user();

        $query = WaterConnection::withoutGlobalScope(WaterConnection::SCOPE_NOT_CANCELED)
            ->where('water_connections.locality_id', $authUser->locality_id)
            ->join('customers', 'water_connections.customer_id', '=', 'customers.id')
            ->orderBy('water_connections.created_at', 'desc')
            ->select('water_connections.*');

        if ($request->has('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('water_connections.id', 'LIKE', "%{$search}%")
                ->orWhere('water_connections.name', 'LIKE', "%{$search}%")
                ->orWhere('water_connections.type', $search)
                ->orWhere('customers.name', 'LIKE', "%{$search}%")
                ->orWhere('customers.last_name', 'LIKE', "%{$search}%");
            });
        }

        $connections = $query->paginate(10);
        $customers = Customer::where('locality_id', $authUser->locality_id)->get();
        $costs = Cost::where('locality_id', $authUser->locality_id)->get();

        return view('waterConnections.index', compact('connections', 'customers', 'costs'));
    }

    public function store(Request $request)
    {
        $authUser = auth()->user();

        $waterConnectionData = $request->all();

        $waterConnectionData['water_days'] = json_encode(
            $request->has('all_days') ? 'all' : $request->input('days', [])
        );

        $waterConnectionData['locality_id'] = $authUser->locality_id;
        $waterConnectionData['created_by'] = $authUser->id;

        WaterConnection::create($waterConnectionData);

        return redirect()->route('waterConnections.index')->with('success', 'Toma de agua registrada correctamente.');
    }

    public function show($id)
    {
        $connections = WaterConnection::findOrFail($id);
        return view('waterConnections.show', compact('connections'));
    }

    public function update(Request $request, $id)
    {
        $connection = WaterConnection::find($id);

        if (!$connection) {
            return redirect()->back()->with('error', 'Toma de Agua no encontrada.');
        }

        $connection->name = $request->input('nameUpdate');
        $connection->customer_id = $request->input('customerIdUpdate');
        $connection->type = $request->input('typeUpdate');
        $connection->occupants_number = $request->input('occupantsNumberUpdate');

        $connection->water_days = json_encode(
            $request->has('all_days_update') ? 'all' : $request->input('days_update', [])
        );

        $connection->street = $request->input('streetUpdate');
        $connection->block = $request->input('blockUpdate');
        $connection->exterior_number = $request->input('exteriorNumberUpdate');
        $connection->interior_number = $request->input('interiorNumberUpdate');
        $connection->has_water_pressure = $request->input('hasWaterPressureUpdate');
        $connection->has_cistern = $request->input('hasCisternUpdate');
        $connection->cost_id = $request->input('costIdUpdate');
        $connection->note = $request->input('noteUpdate');

        $connection->save();

        return redirect()->route('waterConnections.index')->with('success', 'Toma de Agua actualizada correctamente.');
    }

    public function destroy($id)
    {
        $connection = WaterConnection::find($id);
        $connection->delete();
        return redirect()->route('waterConnections.index')->with('success', 'Toma de Agua eliminada correctamente.');
    }

    public function cancel(Request $request, $id)
    {
        $connection = WaterConnection::findOrFail($id);

        if ($connection->hasDebt()) {
            return redirect()->route('waterConnections.index')->with('debtError', true)->with('connectionName', $connection->name);
        }

        $connection->cancel_description = $request->input('cancelDescription');
        $connection->canceled_at = now();
        $connection->is_canceled = true;
        $connection->save();

        return redirect()->route('waterConnections.index')->with('success', 'Toma cancelada correctamente.');
    }

    public function reactivate(Request $request, $id)
    {
        $connection = WaterConnection::withoutGlobalScope(WaterConnection::SCOPE_NOT_CANCELED)->findOrFail($id);

        if ($request->has('customer_id')) {
            $connection->customer_id = $request->input('customer_id');
        }

        $connection->is_canceled = false;
        $connection->canceled_at = null;
        $connection->cancel_description = null;
        $connection->save();

        return redirect()->route('waterConnections.index')->with('success', 'Toma reactivada y asignada correctamente.');
    }
    
    public function generateQrAjax($id)
    {

    try {
        $connection = WaterConnection::findOrFail($id);
        $encryptedId = Crypt::encryptString($id);
        $safeEncryptedId = urlencode($encryptedId);
        $publicUrl = route('waterConnections.public.form', ['code' => $safeEncryptedId]);
        
        $qrCode = base64_encode(QrCode::format('svg')
            ->size(300)
            ->margin(2)
            ->errorCorrection('H')
            ->generate($publicUrl));
        
        $downloadUrl = route('waterConnections.qr-download', $id);

        return response()->json([
            'success' => true,
            'image' => 'data:image/svg+xml;base64,' . $qrCode,
            'download_url' => $downloadUrl
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error generando QR: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Error al generar el código QR: ' . $e->getMessage()], 500);
    }
    }

    public function downloadQr($id)
    {

    try {
        $connection = WaterConnection::findOrFail($id);
        
        $encryptedId = Crypt::encryptString($id);
        $safeEncryptedId = urlencode($encryptedId);
        
        $publicUrl = route('waterConnections.public.form', ['code' => $safeEncryptedId]);
        
        $qrCode = QrCode::format('png')
            ->size(400)
            ->margin(2)
            ->errorCorrection('H')
            ->generate($publicUrl);
        
        $fileName = "QR_Toma_{$connection->id}.png";
        
        return response($qrCode)->header('Content-Type', 'image/png')->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
            
    } catch (\Exception $e) {
        return back()->with('error', 'Error al descargar el código QR');
    }
    }

    public function showPublicForm($code)
    {

    if (!auth()->check()) {
        return redirect()->route('login')
            ->with('error', 'Debes iniciar sesión para ver esta información');
    }
    
    try {
        $id = Crypt::decryptString($code);

        if (!is_numeric($id) || $id <= 0) {
            abort(404, 'Código no válido');
        }
        
        return view('waterConnections.public-form', compact('id'));
        
    } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
        abort(404, 'Código no válido o expirado');
    } catch (\Exception $e) {
        abort(404, 'Error al procesar el código');
    }
    }

    public function showPublic(Request $request)
    {

    try {
        
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para ver esta información');
        }

        $request->validate([
            'id' => 'required|integer|exists:water_connections,id'
        ]);

        $connection = WaterConnection::with(['customer', 'locality'])->findOrFail($request->id);
        
        return view('waterConnections.public', compact('connection'));
        
    } catch (\Exception $e) {
        abort(404, 'Toma de agua no encontrada');
    }
    }
}