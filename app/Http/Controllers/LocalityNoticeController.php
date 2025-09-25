<?php

namespace App\Http\Controllers;

use App\Models\LocalityNotice;
use App\Models\Locality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LocalityNoticeController extends Controller
{
    public function index(Request $request)
    {
        $userLocalityId = auth()->user()->locality_id;
        $query = LocalityNotice::with(['creator', 'locality'])
            ->where('locality_id', $userLocalityId)
            ->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->input('is_active'));
        }

        $localityNotices = $query->paginate(10);
        $localities = Locality::where('id', $userLocalityId)->get();

        return view('localityNotices.index', compact('localityNotices', 'localities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,xlsx,xls|max:10240'
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->startOfDay();
        $today = now()->startOfDay();
        
        if ($startDate->lt($today)) {
            return redirect()->back()->with('error', 'La fecha de inicio no puede ser anterior a hoy.')->withInput();
        }
        
        if ($endDate->lt($today)) {
            return redirect()->back()->with('error', 'La fecha de fin no puede ser anterior a hoy.')->withInput();
        }

        $localityNotice = LocalityNotice::create([
            'created_by' => auth()->id(),
            'locality_id' => auth()->user()->locality_id,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'is_active' => true
        ]);

        if ($request->hasFile('attachment')) {
            $localityNotice->addMediaFromRequest('attachment')->toMediaCollection('notice_attachments');
        }

        return redirect()->route('localityNotices.index')->with('success', 'Aviso de localidad creado correctamente.');
    }

    public function show($id)
    {
        $localityNotice = LocalityNotice::with(['creator', 'locality'])->findOrFail($id);
        return view('localityNotices.show', compact('localityNotice'));
    }

    public function update(Request $request, $id)
    {
        $localityNotice = LocalityNotice::find($id);
        
        if ($localityNotice) {
            $request->validate([
                'title' => 'required|string|max:100',
                'description' => 'required|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240'
            ]);

            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->startOfDay();
            $today = now()->startOfDay();
            
            $currentStartDate = Carbon::parse($localityNotice->start_date)->startOfDay();
            
            if ($startDate->lt($today) && !$startDate->equalTo($currentStartDate)) {
                return redirect()->back()->with('error', 'La fecha de inicio no puede ser anterior a hoy.')->withInput();
            }
            
            if ($endDate->lt($today)) {
                return redirect()->back()->with('error', 'La fecha de fin no puede ser anterior a hoy.')->withInput();
            }

            $localityNotice->title = $request->input('title');
            $localityNotice->description = $request->input('description');
            $localityNotice->start_date = $request->input('start_date');
            $localityNotice->end_date = $request->input('end_date');
            $localityNotice->is_active = $request->input('is_active', true);

            if ($request->has('remove_attachment')) {
                $localityNotice->clearMediaCollection('notice_attachments');
            }

            if ($request->hasFile('attachment')) {
                $localityNotice->clearMediaCollection('notice_attachments');
                $localityNotice->addMediaFromRequest('attachment')
                            ->toMediaCollection('notice_attachments');
            }

            $localityNotice->save();
            return redirect()->route('localityNotices.index')->with('success', 'Aviso de localidad actualizado correctamente.');
        }

        return redirect()->back()->with('error', 'Aviso de localidad no encontrado.');
    }

    public function destroy($id)
    {
        $localityNotice = LocalityNotice::find($id);
        
        if ($localityNotice) {
            $localityNotice->clearMediaCollection('notice_attachments');
            $localityNotice->delete();
            return redirect()->route('localityNotices.index')
                ->with('success', 'Aviso de localidad eliminado correctamente.');
        }

        return redirect()->back()->with('error', 'Aviso de localidad no encontrado.');
    }

    public function toggleStatus($id)
    {
        $localityNotice = LocalityNotice::find($id);
        
        if ($localityNotice) {
            $now = now();
            $endDate = Carbon::parse($localityNotice->end_date);
            
            if ($endDate->lt($now)) {
                if ($localityNotice->is_active) {
                    $localityNotice->is_active = false;
                    $localityNotice->save();
                    
                    return redirect()->back()->with('warning', 'El aviso ha expirado y ha sido desactivado automáticamente.');
                } else {
                    return redirect()->back()->with('error', 'No se puede activar un aviso que ya ha expirado.');
                }
            }
            
            $localityNotice->is_active = !$localityNotice->is_active;
            $localityNotice->save();
            $status = $localityNotice->is_active ? 'activado' : 'desactivado';

            return redirect()->back()->with('success', "Aviso de localidad {$status} correctamente.");
        }

        return redirect()->back()->with('error', 'Aviso de localidad no encontrado.');
    }

    public function downloadAttachment($id)
    {
        try {
            $localityNotice = LocalityNotice::findOrFail($id);
            
            if (!$localityNotice->hasMedia('notice_attachments')) {
                return response()->json(['error' => 'No hay archivo adjunto'], 404);
            }

            $media = $localityNotice->getFirstMedia('notice_attachments');
            $filePath = $media->getPath();
            
            if (!file_exists($filePath)) {
                return response()->json(['error' => 'Archivo no encontrado: ' . $filePath], 404);
            }

            header('Content-Type: ' . $media->mime_type);
            header('Content-Disposition: inline; filename="' . $media->file_name . '"');
            readfile($filePath);
            exit;
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function deactivateExpiredNotices()
    {
        $expiredNotices = LocalityNotice::where('is_active', true)->where('end_date', '<', now())->get();

        foreach ($expiredNotices as $notice) {
            $notice->is_active = false;
            $notice->save();
        }

        return $expiredNotices->count();
    }
}