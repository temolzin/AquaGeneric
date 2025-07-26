<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\TokenHandler;


class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->locality) {
            $token = $user->locality->token;

            if (!$token) {
                return redirect()->route('expiredSubscriptions.expired')
                    ->withErrors(['token' => 'Acceso restringido. No se ha asignado un token a esta localidad.']);
            }

            $tokenValidation = TokenHandler::verifyToken($token, $user);

            if (!$tokenValidation['valid']) {
                return redirect()->route('expiredSubscriptions.expired')
                    ->withErrors(['token' => $tokenValidation['error']]);
            }
        }

        return $next($request);
    }
}
