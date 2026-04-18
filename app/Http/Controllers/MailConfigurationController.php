<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Locality;

class MailConfigurationController extends Controller
{
    public function createOrUpdateMailConfigurations(Request $request, $localityId)
    {
        $locality = Locality::findOrFail($localityId);
        $currentConfig = $locality->mailConfiguration;

        $validated = $request->validate([
            'mailer' => 'required|string',
            'host' => 'required|string',
            'port' => 'required|numeric',
            'username' => 'required|string',
            'password' => $currentConfig ? 'nullable|string' :'required|string',
            'encryption' => 'required|string',
            'from_name' => 'nullable|string',
        ]);

        if ($currentConfig && blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        $locality->mailConfiguration()->updateOrCreate(
            ['locality_id' => $locality->id],
            $validated
        );

        return redirect()->route('localities.index')->with('success', '¡Configuración de correo guardada!');
    }
}
