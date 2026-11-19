<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Debt;
use App\Models\Locality;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'customer_id' => Customer::factory(),
            'debt_id' => Debt::factory(),
            'locality_id' => Locality::factory(),
            'created_by' => User::factory(),
            'amount' => $this->faker->randomFloat(2, 10, 100),
            'method' => 'cash',
            'note' => null,
            'is_future_payment' => false,
        ];
    }
}
