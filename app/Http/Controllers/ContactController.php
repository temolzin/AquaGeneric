<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\SendContactFormEmail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'message' => 'required|string|max:2000',
        ]);

        SendContactFormEmail::dispatch([
            'name'           => $validated['name'],
            'email'          => $validated['email'],
            'contactMessage' => $validated['message'],
        ]);

        return redirect()->back()->with('contact_success', 'Tu mensaje ha sido enviado correctamente. Nos pondremos en contacto contigo pronto.');
    }
}
