<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use App\Models\TokenHandler;

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

        $user = Auth::user();
        $token = $request->input('token');

        $newTokenValidation = TokenHandler::verifyToken($token, $user);

        if ($newTokenValidation['valid']) {
            $user->locality->token = $token;
            $user->locality->save();

            session()->flash('success', 'Token actualizado correctamente.');

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'error' => $newTokenValidation['error']]);
    }
}
