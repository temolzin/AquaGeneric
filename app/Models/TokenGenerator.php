<?php

namespace App\Models;

use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class TokenGenerator
{
    public static function generateTokenForLocality($localityId, $startDate = null, $endDate = null)
    {
        $startDate = $startDate ? Carbon::parse($startDate)->format('Y-m-d') : now()->format('Y-m-d');
        $endDate = $endDate ? Carbon::parse($endDate)->format('Y-m-d') : now()->addYear()->format('Y-m-d');

        $data = [
            'idLocality' => $localityId,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];

        $hmacSignature = hash_hmac('sha256', json_encode($data), env('TOKEN_SECRET_KEY'));

        return Crypt::encrypt([
            'data' => $data,
            'hmac' => $hmacSignature,
        ]);
    }
}
