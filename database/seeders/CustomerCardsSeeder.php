<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\CustomerCard;

class CustomerCardsSeeder extends Seeder
{
    public function run()
    {
        // Get customers with user_id (excluding Alonso which is handled by AlonsoSeeder)
        $customers = Customer::whereNotNull('user_id')
            ->where('user_id', '!=', 5)
            ->limit(5)
            ->get();

        if ($customers->isEmpty()) {
            return;
        }

        foreach ($customers as $customer) {
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
                    'alias' => 'MasterCard',
                    'openpay_card_id' => 'test_sandbox_mastercard_4444',
                    'brand' => 'mastercard',
                    'last_four' => '4444',
                    'holder_name' => $customer->name . ' ' . $customer->last_name,
                    'expiration_month' => '06',
                    'expiration_year' => '27',
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
}
