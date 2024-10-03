<?php

namespace App\Http\Controllers;

use App\Models\Locality;
use Illuminate\Http\Request;

class LocalityController extends Controller
{

    public function index(Request $request)
    {
        $query = Locality::query()->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereRaw("CONCAT(locality_name, ' ', municipality, ' ', zip_code) LIKE ?", ["%{$search}%"]);
        }

        $localities = $query->paginate(10);
        return view('localities.index', compact('localities'));
    }

    public function store(Request $request)
    {
        $customer = Locality::create($request->all());

        if ($request->hasFile('photo')) {
            $customer->addMediaFromRequest('photo')->toMediaCollection('localityGallery');
        }

        return redirect()->route('localities.index')->with('success', 'Localidad registrada correctamente.');
    }

    public function show($id)
    {
        $locality = Locality::findOrFail($id);
        return view('localities.show', compact('locality'));
    }

    public function update(Request $request, $id)
    {
        $locality = Locality::find($id);
        if ($locality) {
            $locality->locality_name = $request->input('localityNameUpdate');
            $locality->municipality = $request->input('municipalityUpdate');
            $locality->state = $request->input('stateUpdate');
            $locality->zip_code = $request->input('zipCodeUpdate');

            $locality->save();

            return redirect()->route('localities.index')->with('success', 'Localidad actualizada correctamente.');
        }

        return redirect()->back()->with('error', 'Localidad no encontrada.');
    }

    public function destroy(Locality $locality)
    {
        $locality->delete();
        return redirect()->route('localities.index')->with('success', 'Localidad eliminada correctamente.');
    }

    public function updateLogo(Request $request, $id){
        $locality = Locality::find($id);
        if ($locality) {
            
            if ($request->hasFile('photo')) {
                $locality->clearMediaCollection('localityGallery');
                $locality->addMediaFromRequest('photo')->toMediaCollection('localityGallery');
            }
            return redirect()->route('localities.index')->with('success', 'Logo de Localidad actualizado correctamente.');
        }

        return redirect()->back()->with('error', 'Localidad no encontrada.');
    }
}
