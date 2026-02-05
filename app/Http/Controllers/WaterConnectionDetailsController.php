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

        $connection = WaterConnection::withoutGlobalScope(WaterConnection::SCOPE_NOT_CANCELED)
            ->where('locality_id', $authUser->locality_id)
            ->findOrFail($id);

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

        return view('waterConnections.tabs.history', compact('connection', 'transfers'));
    }
}
