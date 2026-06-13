<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendContactFormEmail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|max:255',
            'message'               => 'required|string|max:2000',
            'g-recaptcha-response'  => 'required|string',
        ], [
            'g-recaptcha-response.required' => 'Por favor completa el captcha antes de enviar el mensaje.',
        ]);

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => config('services.recaptcha.secret'),
            'response' => $validated['g-recaptcha-response'],
            'remoteip' => $request->ip(),
        ]);

        $recaptchaBody = $response->json();

        Log::info('reCAPTCHA verification result', [
            'remote_ip' => $request->ip(),
            'success' => $recaptchaBody['success'] ?? false,
            'response' => $recaptchaBody,
            'error_codes' => $recaptchaBody['error-codes'] ?? null,
        ]);

        if (!($recaptchaBody['success'] ?? false)) {
            Log::warning('reCAPTCHA verification failed', [
                'remote_ip' => $request->ip(),
                'response' => $recaptchaBody,
                'error_codes' => $recaptchaBody['error-codes'] ?? null,
            ]);

            return redirect()->back()
                ->withInput()
                ->withErrors(['captcha' => 'La verificación de reCAPTCHA falló. Por favor inténtalo de nuevo.']);
        }

        SendContactFormEmail::dispatch([
            'name'           => $validated['name'],
            'email'          => $validated['email'],
            'contactMessage' => $validated['message'],
        ]);

        return redirect()->back()->with('contact_success', 'Tu mensaje ha sido enviado correctamente. Nos pondremos en contacto contigo pronto.');
    }
}
