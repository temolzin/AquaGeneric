<?php

namespace App\Http\Controllers;

use App\Models\LogInventory;
use Illuminate\Http\Request;

class LogInventoryController extends Controller
{

    public function store(Request $request)
    {
        $authUser = auth()->user();

        $logInventoryData = [
            'locality_id' => $authUser->locality_id,
            'created_by' => $authUser->id,
            'amount' => $request->input('amount'),
            'description' => $request->input('description'),
            'inventory_id' => $request->inventoryId,
        ];

        $logInventory = LogInventory::create($logInventoryData);

        return redirect()->route('inventory.index')->with('success', 'Cantidad actualizada correctamente');
    }

}
