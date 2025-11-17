<?php

namespace App\Http\Controllers;

use App\Models\Locality;
use App\Models\Membership;
use App\Models\Token;
use App\Models\MailConfiguration;
use Illuminate\Http\Request; 
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
        $memberships = Membership::all();

        $mailExamples = [
            'mailer'  => MailConfiguration::EXAMPLE_MAILER,
            'host'  => MailConfiguration::EXAMPLE_HOST,
            'port'  => MailConfiguration::EXAMPLE_PORT,
            'username'  => MailConfiguration::EXAMPLE_USERNAME,
            'password'  => MailConfiguration::EXAMPLE_PASSWORD,
            'encryption'  => MailConfiguration::EXAMPLE_ENCRYPTION,
            'from_name'  => MailConfiguration::EXAMPLE_FROM_NAME,
        ];

        return view('localities.index', compact('localities','mailExamples', 'memberships'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'municipality' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|string|max:10',
            'membership_id' => 'nullable|exists:memberships,id'
        ]);

        $locality = Locality::create($request->all());

        if ($request->hasFile('photo')) {
            $locality->addMediaFromRequest('photo')->toMediaCollection('localityGallery');
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
            $request->validate([
                'membership_id' => 'nullable|exists:memberships,id'
            ]);

            $locality->name = $request->input('localityNameUpdate');
            $locality->municipality = $request->input('municipalityUpdate');
            $locality->state = $request->input('stateUpdate');
            $locality->zip_code = $request->input('zipCodeUpdate');
            if ($locality->membership_id != $request->input('membership_id')) {
                $locality->membership_assigned_at = now();
            }
            
            $locality->membership_id = $request->input('membership_id');
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
            'membership_id' => 'required|exists:memberships,id',
        ]);

        $id = $request->input('idLocality');
        $startDate = $request->input('startDate');
        $membershipId = $request->input('membership_id');

        $locality = Locality::findOrFail($id);
        
        $locality->update([
            'membership_id' => $membershipId,
            'membership_assigned_at' => now()
        ]);

        $membership = Membership::find($membershipId);
        $endDate = \Carbon\Carbon::parse($startDate)->addMonths($membership->term_months);

        $token = Token::generateTokenForLocality($id, $startDate, $endDate->toDateString());

        return redirect()->route('localities.index')
            ->with('createdToken', $token)
            ->with('localityName', $locality->name)
            ->with('success', 'Token generado y membresía asignada correctamente.');
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
