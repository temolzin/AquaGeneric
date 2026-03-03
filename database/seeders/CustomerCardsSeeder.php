<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\CustomerCard;
use App\Models\User;

class CustomerCardsSeeder extends Seeder
{

    public function run()
    {
        $clientUser = User::where('email', 'alonso@gmail.com')->firstOrFail();

        $customer = Customer::where('locality_id', 1)
            ->orderBy('id')
            ->firstOrFail();

        if (!$customer->user_id) {
            $customer->update(['user_id' => $clientUser->id]);
        }

        CustomerCard::where('customer_id', $customer->id)->delete();

        $cards = [
            [
                'alias' => 'Mi Visa',
                'openpay_card_id' => 'test_sandbox_visa_1111',
                'brand' => 'visa',
                'last_four' => '1111',
                'holder_name' => $customer->name . ' ' . $customer->last_name,
                'expiration_month' => '12',
                'expiration_year' => '28',
                'is_default' => true,
            ],
            [
                'alias' => 'MasterCard Santander',
                'openpay_card_id' => 'test_sandbox_mastercard_4444',
                'brand' => 'mastercard',
                'last_four' => '4444',
                'holder_name' => $customer->name . ' ' . $customer->last_name,
                'expiration_month' => '06',
                'expiration_year' => '27',
                'is_default' => false,
            ],
            [
                'alias' => 'AmEx',
                'openpay_card_id' => 'test_sandbox_amex_0007',
                'brand' => 'american_express',
                'last_four' => '0007',
                'holder_name' => $customer->name . ' ' . $customer->last_name,
                'expiration_month' => '09',
                'expiration_year' => '26',
                'is_default' => false,
            ],
            [
                'alias' => 'Error 3001 - Rechazada',
                'openpay_card_id' => 'test_sandbox_error_3001',
                'brand' => 'visa',
                'last_four' => '2220',
                'holder_name' => $customer->name . ' ' . $customer->last_name,
                'expiration_month' => '12',
                'expiration_year' => '28',
                'is_default' => false,
            ],
            [
                'alias' => 'Error 3002 - Expirada',
                'openpay_card_id' => 'test_sandbox_error_3002',
                'brand' => 'visa',
                'last_four' => '0069',
                'holder_name' => $customer->name . ' ' . $customer->last_name,
                'expiration_month' => '01',
                'expiration_year' => '20',
                'is_default' => false,
            ],
        ];

        foreach ($cards as $cardData) {
            CustomerCard::create([
                'customer_id' => $customer->id,
                'alias' => $cardData['alias'],
                'openpay_card_id' => $cardData['openpay_card_id'],
                'brand' => $cardData['brand'],
                'last_four' => $cardData['last_four'],
                'holder_name' => $cardData['holder_name'],
                'expiration_month' => $cardData['expiration_month'],
                'expiration_year' => $cardData['expiration_year'],
                'is_default' => $cardData['is_default'],
            ]);
        }

    }
}
