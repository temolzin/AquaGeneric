<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use App\Jobs\SendContactFormEmail;

class HomeContactController extends Controller
{
    public function send(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:2000',
        ]);

        // Dispatch a queued job to send the contact email.
        try {
            SendContactFormEmail::dispatch($data);
            return back()->with('success', 'Tu mensaje ha sido enviado y será procesado. Gracias por contactarnos.');
        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error al encolar el envío. Intenta más tarde.');
        }
    }
}
