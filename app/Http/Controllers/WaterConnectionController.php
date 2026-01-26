<?php

namespace App\Http\Controllers;

use App\Models\WaterConnection;
use App\Models\User;
use App\Models\Cost;
use App\Models\Section;
use App\Models\Locality;
use App\Models\Customer;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class WaterConnectionController extends Controller
{
    public function index(Request $request)
    {
        $authUser = auth()->user();

        $query = WaterConnection::withoutGlobalScope(WaterConnection::SCOPE_NOT_CANCELED)
            ->where('water_connections.locality_id', $authUser->locality_id)
            ->join('customers', 'water_connections.customer_id', '=', 'customers.id')
            ->leftJoin('sections', 'water_connections.section_id', '=', 'sections.id')
            ->orderBy('water_connections.created_at', 'desc')
            ->select('water_connections.*', 'customers.status as customer_status');

        if ($request->has('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('water_connections.id', 'LIKE', "%{$search}%")
                ->orWhere('water_connections.name', 'LIKE', "%{$search}%")
                ->orWhere('water_connections.type', $search)
                ->orWhere('customers.name', 'LIKE', "%{$search}%")
                ->orWhere('customers.last_name', 'LIKE', "%{$search}%")
                ->orWhere('sections.name', 'LIKE', "%{$search}%");
            });
        }

        $connections = $query->paginate(10);

        $customers = Customer::where('locality_id', $authUser->locality_id)->get();
        $costs = Cost::where('locality_id', $authUser->locality_id)
                    ->orWhereNull('locality_id')
                    ->get();
        $sections = Section::where('locality_id', $authUser->locality_id)
                    ->orWhereNull('locality_id')
                    ->get();

        return view('waterConnections.index', compact('connections', 'customers', 'costs', 'sections'));
    }

    public function store(Request $request)
    {
        $authUser = auth()->user();

        $locality = Locality::with('membership')->find($authUser->locality_id);

        if ($locality && $locality->membership) {
            $currentConnectionsCount = WaterConnection::where('locality_id', $authUser->locality_id)
                ->where('is_canceled', false)
                ->count();

            if ($currentConnectionsCount >= $locality->membership->water_connections_number) {
                return redirect()->back()->with('error',
                    'No se pueden registrar más tomas de agua. Límite de ' .
                    $locality->membership->water_connections_number .
                    ' tomas de agua alcanzado para la localidad '. $locality->name. '. Contacte al administrador para habilitar más tomas de agua.');
            }
        }

        $waterConnectionData = $request->all();

        $waterConnectionData['section_id'] = $request->input('section_id');
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
        $connection = WaterConnection::withoutGlobalScope(WaterConnection::SCOPE_NOT_CANCELED)
            ->findOrFail($id);

        $sections = Section::where('locality_id', $connection->locality_id)
                    ->orWhereNull('locality_id')
                    ->get();
        return view('waterConnections.show', compact('connection', 'sections'));
    }

    public function update(Request $request, $id)
    {
        $connection = WaterConnection::find($id);
        $sections = Section::where('locality_id', $connection->locality_id)
                    ->orWhereNull('locality_id')
                    ->get();

        if (!$connection) {
            return redirect()->back()->with('error', 'Toma de Agua no encontrada.');
        }

        $connection->name = $request->input('nameUpdate');
        $connection->customer_id = $request->input('customerIdUpdate');
        $connection->type = $request->input('typeUpdate');
        $connection->occupants_number = $request->input('occupantsNumberUpdate');
        $connection->section_id = $request->input('section_id');

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

        $authUser = auth()->user();
        $locality = Locality::with('membership')->find($authUser->locality_id);

        if ($locality && $locality->membership) {
            $currentConnectionsCount = WaterConnection::where('locality_id', $authUser->locality_id)
                ->where('is_canceled', false)
                ->count();

            if ($currentConnectionsCount >= $locality->membership->water_connections_number) {
                return redirect()->back()->with('error',
                    'No se puede reactivar la toma de agua. Límite de ' .
                    $locality->membership->water_connections_number .
                    ' tomas de agua alcanzado para esta localidad.');
            }
        }

        if ($request->has('customer_id')) {
            $connection->customer_id = $request->input('customer_id');
        }

        $connection->is_canceled = false;
        $connection->canceled_at = null;
        $connection->cancel_description = null;
        $connection->save();

        return redirect()->route('waterConnections.index')->with('success', 'Toma reactivada y asignada correctamente.');
    }

    private function generateConnectionHash($id)
    {
        return hash('sha256', $id . env('APP_KEY', 'default-secret-key'));
    }

    private function getIdFromHash($hash)
    {
        $connections = WaterConnection::withoutGlobalScope(WaterConnection::SCOPE_NOT_CANCELED)
            ->select('id')
            ->get();

        foreach ($connections as $connection) {
            if ($this->generateConnectionHash($connection->id) === $hash) {
                return $connection->id;
            }
        }
        return null;
    }

    public function showPublic($hash)
    {
        try {
            $id = $this->getIdFromHash($hash);

            if (!$id) {
                abort(404, 'Toma de agua no encontrada');
            }

            $connection = WaterConnection::withoutGlobalScope(WaterConnection::SCOPE_NOT_CANCELED)
                ->with(['customer', 'locality'])
                ->find($id);

            if (!$connection) {
                abort(404, 'Toma de agua no encontrada');
            }

            if (!$connection->locality) {
                abort(404, 'Localidad no encontrada para esta toma de agua');
            }

            return view('water_connections_info_qr', compact('connection'));

        } catch (\Exception $e) {
            abort(404, 'Error al cargar la información de la toma de agua');
        }
    }

    public function generateQrAjax($id)
    {
        try {
            $connection = WaterConnection::withoutGlobalScope(WaterConnection::SCOPE_NOT_CANCELED)
                ->findOrFail($id);

            $hash = $this->generateConnectionHash($id);
            $publicUrl = route('waterConnections.public', ['hash' => $hash]);

            $qrCode = base64_encode(
                QrCode::format('svg')
                    ->size(300)
                    ->margin(2)
                    ->errorCorrection('H')
                    ->generate($publicUrl)
            );

            $downloadUrl = route('waterConnections.qr-download', $id);

            return response()->json([
                'success' => true,
                'image' => 'data:image/svg+xml;base64,' . $qrCode,
                'download_url' => $downloadUrl,
                'public_url' => $publicUrl,
                'hash' => $hash
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar el código QR: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function downloadQr($id)
    {
        try {
            $connection = WaterConnection::withoutGlobalScope(WaterConnection::SCOPE_NOT_CANCELED)
                ->findOrFail($id);

            $hash = $this->generateConnectionHash($id);
            $publicUrl = route('waterConnections.public', ['hash' => $hash]);

            $qrCode = QrCode::format('png')
                ->size(400)
                ->margin(2)
                ->errorCorrection('H')
                ->generate($publicUrl);

            $fileName = "QR_Toma_{$connection->id}.png";

            return response($qrCode)
                ->header('Content-Type', 'image/png')
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al descargar el código QR');
        }
    }

    public function showCustomerWaterConnections()
    {
        $authUser = auth()->user();

        $customer = $authUser->customer;

        if (!$customer) {
            $connections = collect();
        } else {
            $query = WaterConnection::with(['cost', 'locality'])
                ->where('customer_id', $customer->id)
                ->where('locality_id', $authUser->locality_id);
            if (request()->has('search') && request('search') != '') {
                $search = request('search');
                $query->where(function($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('street', 'like', "%{$search}%")
                    ->orWhere('block', 'like', "%{$search}%");
                });
            }
            /** @var \Illuminate\Pagination\LengthAwarePaginator $connections */
            $connections = $query->paginate(10)->appends(request()->query());

            $connections->getCollection()->transform(function ($connection) {
                $connection->formatted_water_days = $this->getFormattedWaterDays($connection->water_days);
                $connection->water_pressure_text = $connection->has_water_pressure ? 'Sí' : 'No';
                $connection->cistern_text = $connection->has_cistern ? 'Sí' : 'No';
                return $connection;
            });
        }

        return view('viewCustomerWaterConnections.index', compact('connections'));
    }

    private function getFormattedWaterDays($waterDays)
    {
        if (empty($waterDays) || $waterDays === 'null' || $waterDays === '[]') {
            return 'No hay días específicos asignados';
        }

        if ($waterDays === '"all"' || $waterDays === 'all') {
            return 'Todos los días';
        }

        $daysArray = json_decode($waterDays, true) ?: [$waterDays];

        $daysMap = [
            'monday' => 'Lunes', 'tuesday' => 'Martes', 'wednesday' => 'Miércoles',
            'thursday' => 'Jueves', 'friday' => 'Viernes', 'saturday' => 'Sábado',
            'sunday' => 'Domingo'
        ];

        $spanishDays = [];
        foreach ($daysArray as $day) {
            $dayLower = strtolower(trim($day));

            foreach ($daysMap as $en => $es) {
                if ($dayLower === $en || $dayLower === strtolower($es) || $dayLower === 'all') {
                    $spanishDays[] = $es;
                }
            }
        }

        return empty($spanishDays) ? 'No hay días activos' : implode(', ', array_unique($spanishDays));
    }
}
