<?php

namespace App\Http\Controllers;

use App\Models\WaterConnection;
use App\Models\LogWaterConnectionTransfer;
use Illuminate\Http\Request;
use App\Models\Debt;

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

    public function debts($id)
    {
        $authUser = auth()->user();

        $connection = WaterConnection::withoutGlobalScope(WaterConnection::SCOPE_NOT_CANCELED)
            ->where('locality_id', $authUser->locality_id)
            ->findOrFail($id);

        $debts = Debt::query()
            ->where('water_connection_id', $connection->id)
            ->where('locality_id', $authUser->locality_id)
            ->whereIn('status', [Debt::STATUS_PENDING, Debt::STATUS_PARTIAL])
            ->with(['creator:id,name,last_name'])
            ->withSum('payments as payments_sum_amount', 'amount')
            ->orderByDesc('start_date')
            ->get();

        $totalAmount = $debts->sum('amount');
        $totalPaid = $debts->sum(fn($d) => (float)($d->payments_sum_amount ?? 0));
        $totalPending = $totalAmount - $totalPaid;

        return view('waterConnections.tabs.debts', compact(
            'connection',
            'debts',
            'totalAmount',
            'totalPaid',
            'totalPending'
        ));
    }
}
