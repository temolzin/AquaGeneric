<?php
namespace App\Models;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class TokenHandler
{
    public static function verifyToken($token, $user = null)
    {
        try {
            $decrypted = Crypt::decrypt($token);
            $data = $decrypted['data'] ?? null;
            $hmac = $decrypted['hmac'] ?? null;

            if (!$data || !$hmac) {
                return ['valid' => false, 'error' => 'Token incompleto.'];
            }

            $calculatedHmac = hash_hmac('sha256', json_encode($data), env('TOKEN_SECRET_KEY'));
            if ($hmac !== $calculatedHmac) {
                return ['valid' => false, 'error' => 'Token manipulado o inválido.'];
            }

            if (isset($data['endDate'])) {
                $expiration = Carbon::parse($data['endDate'])->startOfDay();
                $today = now()->startOfDay();

                if ($today->gt($expiration)) {
                    return ['valid' => false, 'error' => 'Token expirado.'];
                }
            }

            if ($user && isset($data['idLocality']) && $data['idLocality'] != $user->locality->id) {
                return ['valid' => false, 'error' => 'El token no pertenece a esta localidad.'];
            }

            return ['valid' => true, 'data' => $data];

        } catch (Exception $e) {
            return ['valid' => false, 'error' => 'Token corrupto.'];
        }
    }
}
