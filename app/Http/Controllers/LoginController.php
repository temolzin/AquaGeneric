<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class LoginController extends Controller
{
    private const WARNING_DAY_FIRST_NOTICE = 12;
    private const WARNING_DAY_FINAL_NOTICE = 3;
    private const MAX_ATTEMPTS   = 2;
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
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $key = $this->throttleKey($request);

        if (Cache::has($key . '_locked')) {
            $secondsLeft = (int) Cache::get($key . '_locked') - time();
            if ($secondsLeft > 0) {
                return back()->withErrors([
                    'email' => "Demasiados intentos fallidos. Por favor espera {$secondsLeft} segundos antes de intentarlo de nuevo.",
                ])->withInput($request->only('email'));
            }
            Cache::forget($key . '_locked');
            Cache::forget($key . '_count');
        }

        if (Auth::attempt($request->only('email', 'password'))) {
            Cache::forget($key . '_count');
            Cache::forget($key . '_locked');

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

        $attempts = (int) Cache::get($key . '_count', 0) + 1;
        Cache::put($key . '_count', $attempts, self::LOCKOUT_SECONDS + 60);

        if ($attempts >= self::MAX_ATTEMPTS) {
            Cache::put($key . '_locked', time() + self::LOCKOUT_SECONDS, self::LOCKOUT_SECONDS + 60);
            Cache::put($key . '_count', 0, self::LOCKOUT_SECONDS + 60);

            return back()->withErrors([
                'email' => 'Demasiados intentos fallidos. Por favor espera ' . self::LOCKOUT_SECONDS . ' segundos antes de intentarlo de nuevo.',
            ])->withInput($request->only('email'));
        }

        $remaining = self::MAX_ATTEMPTS - $attempts;

        return back()->withErrors([
            'email' => "Las credenciales proporcionadas no coinciden con nuestros registros. Te queda(n) {$remaining} intento(s).",
        ])->withInput($request->only('email'));
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
