<?php
namespace App\Models;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class TokenHandler
{
    public static function verifyToken($token, $user)
    {
        $errors = [];

        try {

            $decrypted = Crypt::decrypt($token);
            $data = $decrypted['data'] ?? null;
            $hmac = $decrypted['hmac'] ?? null;

            if (!$data || !$hmac) {
                $errors[] = 'Token incompleto.';
            }

            $calculatedHmac = hash_hmac('sha256', json_encode($data), env('TOKEN_SECRET_KEY'));
            if ($hmac !== $calculatedHmac) {
                $errors[] = 'Token manipulado o inválido.';
            }

            if (isset($data['endDate'])) {
                $expiration = Carbon::parse($data['endDate'])->startOfDay();
                $today = now()->startOfDay();

                if ($today->gt($expiration)) {
                    $errors[] = 'Token expirado.';
                }
            }

            if (isset($data['idLocality']) && $data['idLocality'] != $user->locality->id) {
                $errors[] = 'El token no pertenece a esta localidad.';
            }

            if (!empty($errors)) {
                return ['valid' => false, 'error' => implode(' ', $errors)];
            }

            return ['valid' => true, 'data' => $data];

        } catch (Exception $e) {
            return ['valid' => false, 'error' => 'Token corrupto.'];
        }
    }
}

