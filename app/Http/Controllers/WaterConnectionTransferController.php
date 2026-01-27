<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\WaterConnection;
use App\Models\LogWaterConnectionTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WaterConnectionTransferController extends Controller
{
    public function store(Request $request, $id)
    {
        $waterConnection = WaterConnection::with('customer')->findOrFail($id);

        if (!$waterConnection->customer || (int)$waterConnection->customer->status !== 0) {
            return redirect()
                ->route('waterConnections.index')
                ->with('error', 'Solo se puede transferir una toma cuando el titular actual está fallecido.');
        }

        $request->validate([
            'new_customer_id' => 'required|exists:customers,id',
            'note' => 'nullable|string',
        ]);

        $newCustomerId = (int) $request->new_customer_id;
        $oldCustomerId = (int) $waterConnection->customer_id;

        if ($newCustomerId === $oldCustomerId) {
            return back()->with('error', 'El nuevo titular no puede ser el mismo que el titular actual.');
        }

        DB::transaction(function () use ($waterConnection, $oldCustomerId, $newCustomerId, $request) {
            LogWaterConnectionTransfer::create([
                'water_connection_id' => $waterConnection->id,
                'old_customer_id' => $oldCustomerId,
                'new_customer_id' => $newCustomerId,
                'reason' => 'death',
                'effective_date' => now()->toDateString(),
                'note' => $request->note,
                'created_by' => Auth::id(),
            ]);

            $waterConnection->update([
                'customer_id' => $newCustomerId,
            ]);
        });

        return redirect()
            ->route('waterConnections.index')
            ->with('success', 'La toma se transfirió correctamente al nuevo titular.');
    }

}
