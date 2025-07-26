<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Locality;

class MailConfigurationController extends Controller
{
    public function createOrUpdateMailConfigurations(Request $request, $localityId)
    {
        $validated = $request->validate([
            'mailer' => 'required|string',
            'host' => 'required|string',
            'port' => 'required|numeric',
            'username' => 'required|string',
            'password' => 'required|string',
            'encryption' => 'required|string',
            'from_name' => 'nullable|string',
        ]);

        $locality = Locality::findOrFail($localityId);

        $locality->mailConfiguration()->updateOrCreate([], $validated);

        return redirect()->route('localities.index')->with('success', '¡Configuración de correo guardada!');
    }
}
