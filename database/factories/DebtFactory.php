<?php

namespace Database\Factories;

use App\Models\Debt;
use App\Models\DebtCategory;
use App\Models\Locality;
use App\Models\User;
use App\Models\WaterConnection;
use Illuminate\Database\Eloquent\Factories\Factory;

class DebtFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Debt::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'water_connection_id' => WaterConnection::factory(),
            'locality_id' => Locality::factory(),
            'created_by' => User::factory(),
            'debt_category_id' => DebtCategory::serviceId(),
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->endOfMonth()->toDateString(),
            'amount' => $this->faker->randomFloat(2, 50, 500),
            'debt_current' => 0,
            'status' => Debt::STATUS_PENDING,
            'note' => null,
        ];
    }

    /**
     * Indicate that the debt is partially or fully paid.
     *
     * @param  float  $amount
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function paid(?float $amount = null)
    {
        return $this->state(function (array $attributes) use ($amount) {
            $total = $attributes['amount'] ?? 100;
            $paid = $amount === null ? $total : $amount;

            return [
                'debt_current' => $paid,
                'status' => $paid >= $total ? Debt::STATUS_PAID : Debt::STATUS_PARTIAL,
            ];
        });
    }

    /**
     * Set the debt to a future billing period.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function future()
    {
        return $this->state(function (array $attributes) {
            return [
                'start_date' => now()->addMonthNoOverflow()->startOfMonth()->toDateString(),
                'end_date' => now()->addMonthNoOverflow()->endOfMonth()->toDateString(),
            ];
        });
    }
}
