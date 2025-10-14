<?php

namespace App\Http\Controllers;

use App\Models\Locality;
use Illuminate\Http\Request;
use App\Models\Token;
use App\Models\MailConfiguration;
use Illuminate\Support\Facades\Crypt;

class LocalityController extends Controller
{

    public function index(Request $request)
    {
        $query = Locality::query()->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereRaw("CONCAT(name, ' ', municipality, ' ', zip_code) LIKE ?", ["%{$search}%"]);
        }

        $localities = $query->paginate(10);

        $mailExamples = [
            'mailer'  => MailConfiguration::EXAMPLE_MAILER,
            'host'  => MailConfiguration::EXAMPLE_HOST,
            'port'  => MailConfiguration::EXAMPLE_PORT,
            'username'  => MailConfiguration::EXAMPLE_USERNAME,
            'password'  => MailConfiguration::EXAMPLE_PASSWORD,
            'encryption'  => MailConfiguration::EXAMPLE_ENCRYPTION,
            'from_name'  => MailConfiguration::EXAMPLE_FROM_NAME,
        ];
        
        return view('localities.index', compact('localities','mailExamples'));
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
            $locality->name = $request->input('localityNameUpdate');
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

    public function generateToken(Request $request)
    {
        $request->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
        ]);

        $id = $request->input('idLocality');
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
      
        $token = Token::generateTokenForLocality($id, $startDate, $endDate);

        $locality = Locality::find($id);

        return redirect()->route('localities.index')
            ->with('createdToken', $token)
            ->with('localityName', $locality->name);
    }

    public function updatePdfBackground(Request $request, $id)
    {
        $locality = Locality::find($id);

        if (!$locality) {
            return redirect()->back()->with('error', 'Localidad no encontrada.');
        }

        if ($request->hasFile('pdf_background_vertical')) {
            $locality->clearMediaCollection('pdfBackgroundVertical');
            $locality->addMediaFromRequest('pdf_background_vertical')->toMediaCollection('pdfBackgroundVertical');
        }

        if ($request->hasFile('pdf_background_horizontal')) {
            $locality->clearMediaCollection('pdfBackgroundHorizontal');
            $locality->addMediaFromRequest('pdf_background_horizontal')->toMediaCollection('pdfBackgroundHorizontal');
        }

        return redirect()->route('localities.index')->with('success', 'Fondos de reportes actualizados correctamente.');
    }
}
