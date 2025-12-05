<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GeneralEarning;
use App\Models\EarningType;
use App\Models\MovementHistory;

class GeneralEarningController extends Controller
{
    public function index(Request $request)
    {
        $authUser = auth()->user();
        $query = GeneralEarning::where('general_earnings.locality_id', $authUser->locality_id)
            ->whereHas('creator', function ($q) use ($authUser) {
                $q->where('locality_id', $authUser->locality_id);
            })
            ->with(['earningType', 'creator'])
            ->orderBy('general_earnings.created_at', 'desc')
            ->select('general_earnings.*');            
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('general_earnings.concept', 'LIKE', "%{$search}%")
                ->orWhere('general_earnings.id', 'LIKE', "%{$search}%");
            });
        }

        $earnings = $query->paginate(10);
        
        $earningTypes = EarningType::where('locality_id', $authUser->locality_id)
            ->orderBy('name')
            ->orWhereNull('locality_id')
            ->get();

        return view('generalEarnings.index', compact('earnings', 'earningTypes'));
    }

    public function store(Request $request)
    {
        $authUser = auth()->user();

        $generalEarningData = $request->all();

        $generalEarningData['earning_date'] = $request->input('earningDate');
        $generalEarningData['locality_id'] = $authUser->locality_id;
        $generalEarningData['created_by'] = $authUser->id;

        $earning = GeneralEarning::create($generalEarningData);

        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $earning->addMedia($file)->toMediaCollection('earningGallery');
        }

        return redirect()->route('generalEarnings.index')->with('success', 'Ingreso registrado correctamente.');
    }

    public function show($id)
    {
        $authUser = auth()->user();

        $earnings = GeneralEarning::with(['earningType', 'creator'])
            ->where('locality_id', $authUser->locality_id)
            ->findOrFail($id);
        return view('generalEarnings.show', compact('earning'));
    }

    public function update(Request $request, $id)
    {
        $authUser = auth()->user();

        $earning = GeneralEarning::where('locality_id', $authUser->locality_id)
            ->find($id);

        if (!$earning) {
            return redirect()->back()->with('error', 'Ingreso no encontrado.');
        }

        $beforeData = $earning->toArray();

        $earning->concept = $request->input('conceptUpdate');
        $earning->description = $request->input('descriptionUpdate');
        $earning->amount = $request->input('amountUpdate');
        $earning->earning_type_id = $request->input('earning_type_id_update');
        $earning->earning_date = $request->input('earningDateUpdate');

        if ($request->hasFile('receiptUpdate')) {
            $earning->clearMediaCollection('earningGallery');
            $earning->addMedia($request->file('receiptUpdate'))->toMediaCollection('earningGallery');
        }

        $earning->save();

        $afterData = $earning->fresh()->toArray();

        MovementHistory::create([
        'alter_by'    => auth()->id(),
        'module'      => 'general_earnings',
        'action'      => 'update',
        'record_id'   => $earning->id,
        'before_data' => $beforeData,
        'current_data'=> $afterData,
    ]);

        return redirect()->route('generalEarnings.index')->with('success', 'Ingreso actualizado correctamente.');
    }

    public function destroy($id)
    {
        $earning = GeneralEarning::find($id);
        $beforeData = $earning->toArray();
        $earning->delete();

        MovementHistory::create([
        'alter_by'    => auth()->id(),
        'module'      => 'general_earnings',
        'action'      => 'delete',
        'record_id'   => $earning->id,
        'before_data' => $beforeData,
        'current_data'=> null,
    ]);

        return redirect()->route('generalEarnings.index')->with('success', 'Ingreso eliminado correctamente.');
    }

}
