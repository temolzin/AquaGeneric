<?php

namespace Database\Seeders;

use App\Models\Locality;
use Illuminate\Database\Seeder;

class LocalityOpenPaySeeder extends Seeder
{
    public function run()
    {
        $locality = Locality::find(1);

        $locality->update([
            'openpay_merchant_id' => 'mx3gu71ebzx1o4jwlmez',
            'openpay_public_key' => 'pk_41e4fae4a05f465f9204ce6a290778b7',
            'openpay_private_key' => 'sk_c1f6a9d53ecf4c068496dc04d1a6ff40',
            'openpay_webhook_user' => 'aquacontrolmailtesting@gmail.com',
            'openpay_webhook_password' => 'Aqua123456789.',
            'openpay_sandbox' => true,
            'openpay_enabled' => true,
        ]);

    }
}
