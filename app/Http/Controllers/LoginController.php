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

                        if (in_array($daysRemaining, [12, 3])) {
                            return redirect()->intended('dashboard')
                            ->with('warning', 'Tu suscripción vence pronto: ' . $expiration->format('d/m/Y') . ' (faltan ' . $daysRemaining . ' días).');
                        }
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
