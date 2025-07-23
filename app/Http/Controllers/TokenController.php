<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class TokenController extends Controller
{
    /**
     *
     * @return \Illuminate\View\View
     */
    public function showExpired()
    {
        return view('expiredSubscriptions.expired');
    }

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function validateNewToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        try {
            $decrypted = Crypt::decrypt($request->input('token'));
            $data = $decrypted['data'] ?? null;

            if (!$data || !isset($data['idLocality'])) {
                return back()->withErrors(['token' => 'Token inválido o incompleto.']);
            }

            $user = Auth::user();
            $locality = $user->locality;

            if ($locality && $locality->id == $data['idLocality']) {
                $locality->token = $request->input('token');
                $locality->save();

                return redirect()->route('dashboard')->with('success', 'Token actualizado correctamente.');
            }

            return back()->withErrors(['token' => 'Este token no corresponde a tu localidad.']);
        } catch (\Exception $e) {
            return back()->withErrors(['token' => 'Token inválido o corrupto.']);
        }
    }
}
