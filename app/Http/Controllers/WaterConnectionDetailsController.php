<?php

namespace App\Http\Controllers;

use App\Models\WaterConnection;
use App\Models\LogWaterConnectionTransfer;
use Illuminate\Http\Request;

class WaterConnectionDetailsController extends Controller
{
    public function history($id)
    {
        $authUser = auth()->user();

        // 1) Seguridad / multitenancy: asegurar que la toma pertenece a la localidad del usuario
        $connection = WaterConnection::withoutGlobalScope(WaterConnection::SCOPE_NOT_CANCELED)
            ->where('locality_id', $authUser->locality_id)
            ->findOrFail($id);

        // 2) Traer historial con relaciones para evitar N+1
        $transfers = LogWaterConnectionTransfer::query()
            ->where('water_connection_id', $connection->id)
            ->with([
                'oldCustomer:id,name,last_name',
                'newCustomer:id,name,last_name',
                'creator:id,name,last_name',
            ])
            ->orderByDesc('effective_date')
            ->orderByDesc('id')
            ->get();

        // 3) Devolvemos un partial HTML que el tab insertará
        return view('waterConnections.tabs.history', compact('connection', 'transfers'));
    }
}
