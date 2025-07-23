<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
            if (!$user->locality->token) {
                return redirect()->route('expiredSubscriptions.expired')
                    ->withErrors(['token' => 'Acceso restringido. No se ha asignado un token a esta localidad.']);
            }

            $decrypted = Crypt::decrypt($user->locality->token);
            $data = $decrypted['data'] ?? null;
            $hmacSignature = $decrypted['hmac'] ?? null;

            if ($data && isset($data['endDate'])) {
                $expiration = Carbon::parse($data['endDate'])->startOfDay();
                $today = now()->startOfDay();
                $daysRemaining = $today->diffInDays($expiration, false);

                $calculatedHmac = hash_hmac('sha256', json_encode($data), env('TOKEN_SECRET_KEY'));

                if ($hmacSignature !== $calculatedHmac) {
                    return redirect()->route('expiredSubscriptions.expired')->withErrors(['token' => 'Token manipulado o inválido.']);
                }

                if ($daysRemaining < 0) {
                    return redirect()->route('expiredSubscriptions.expired');
                }
            }
        }

        return $next($request);
    }
}
