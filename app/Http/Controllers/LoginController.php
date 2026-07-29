<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Carbon\Carbon;

class LoginController extends Controller
{
    private const WARNING_DAY_FIRST_NOTICE = 12;
    private const WARNING_DAY_FINAL_NOTICE = 3;
    private const MAX_ATTEMPTS   = 3;
    private const LOCKOUT_SECONDS = 300;

    private function throttleKey(Request $request): string
    {
        return 'login_attempts_' . md5($request->ip() . '|' . strtolower($request->input('email', '')));
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validationRules = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        $validationMessages = [];

        if (config('services.recaptcha.site_key') && config('services.recaptcha.secret')) {
            $validationRules['g-recaptcha-response'] = 'required|string';
            $validationMessages['g-recaptcha-response.required'] = 'Por favor completa el captcha.';
        }

        $request->validate($validationRules, $validationMessages);

        if (config('services.recaptcha.site_key') && config('services.recaptcha.secret')) {
            $response = \Illuminate\Support\Facades\Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => config('services.recaptcha.secret'),
                'response' => $request->input('g-recaptcha-response'),
                'remoteip' => $request->ip(),
            ]);

            $recaptchaBody = $response->json();

            if (!($recaptchaBody['success'] ?? false)) {
                \Illuminate\Support\Facades\Log::warning('reCAPTCHA verification failed in LoginController', [
                    'success' => $recaptchaBody['success'] ?? false,
                    'response' => $recaptchaBody,
                    'remoteip' => $request->ip(),
                ]);

                return back()->withErrors([
                    'g-recaptcha-response' => 'La verificación de reCAPTCHA falló. Por favor inténtalo de nuevo.',
                ])->withInput($request->only('email'));
            }
        }

        $key = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $secondsLeft = RateLimiter::availableIn($key);
            session()->flash('lockout_seconds', $secondsLeft);
            return back()->withErrors([
                'password' => "Demasiados intentos fallidos. Por favor espera {$secondsLeft} segundos antes de intentarlo de nuevo.",
            ])->withInput($request->only('email'));
        }

        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();
            RateLimiter::clear($key);

            $user = Auth::user();
            $locality = $user->locality;

            if ($locality && $locality->token) {
                $decrypted = Crypt::decrypt($locality->token);
                $data = $decrypted['data'] ?? null;

                if ($data && isset($data['endDate'])) {
                    $expiration = Carbon::parse($data['endDate'])->startOfDay();
                    $today = now()->startOfDay();
                    $daysRemaining = $today->diffInDays($expiration, false);

                    if ($daysRemaining < 0) {
                        return redirect()->route('expiredSubscriptions.expired');
                    }

                    if (in_array($daysRemaining, [self::WARNING_DAY_FIRST_NOTICE, self::WARNING_DAY_FINAL_NOTICE])) {
                        return redirect()->intended('dashboard')
                            ->with('warning', 'Tu suscripción vence pronto: ' . $expiration->format('d/m/Y') . ' (faltan ' . $daysRemaining . ' días).');
                    }
                }
            }

            return redirect()->intended('dashboard');
        }

        RateLimiter::hit($key, self::LOCKOUT_SECONDS);

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            session()->flash('lockout_seconds', self::LOCKOUT_SECONDS);
            return back()->withErrors([
                'password' => 'Demasiados intentos fallidos. Por favor espera ' . self::LOCKOUT_SECONDS . ' segundos antes de intentarlo de nuevo.',
            ])->withInput($request->only('email'));
        }

        $attempts = RateLimiter::attempts($key);
        $remaining = self::MAX_ATTEMPTS - $attempts;

        return back()->withErrors([
            'password' => "Las credenciales proporcionadas no coinciden con nuestros registros. Te queda(n) {$remaining} intento(s).",
        ])->withInput($request->only('email'));
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
