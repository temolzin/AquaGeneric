<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class LoginController extends Controller
{   
    private const WARNING_DAY_FIRST_NOTICE = 12;
    private const WARNING_DAY_FINAL_NOTICE = 3;

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

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
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

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
