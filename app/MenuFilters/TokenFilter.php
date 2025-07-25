<?php

namespace App\MenuFilters;

use Illuminate\Support\Facades\Auth;
use App\Models\TokenHandler; 

class TokenFilter
{
    public function transform($item, $builder = null)
    {
        $user = Auth::user();

        $expiredSubscription = [
            '/expiredSubscriptions/expired',
            'expiredSubscriptions.expired',
        ];

        if ($user) {
            if ($user->hasRole('Admin')) {
                if (
                    (isset($item['route']) && in_array($item['route'], $expiredSubscription)) ||
                    (isset($item['url']) && in_array($item['url'], $expiredSubscription))
                ) {
                    return false; 
                }

                return $item;
            }

            $tokenValidation = TokenHandler::verifyToken($user->locality->token, $user);

            if (!$tokenValidation['valid']) {
                if (
                    (isset($item['route']) && in_array($item['route'], $expiredSubscription)) ||
                    (isset($item['url']) && in_array($item['url'], $expiredSubscription))
                ) {
                    return $item;
                }

                return false;
            }

            if ($tokenValidation['valid']) {
                if (
                    (isset($item['route']) && in_array($item['route'], $expiredSubscription)) ||
                    (isset($item['url']) && in_array($item['url'], $expiredSubscription))
                ) {
                    return false;
                }

                return $item;
            }
        }

        return $item;
    }
}
