<?php

namespace Database\Factories;

use App\Models\Membership;
use Illuminate\Database\Eloquent\Factories\Factory;

class MembershipFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Membership::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'Membresía '.$this->faker->unique()->word(),
            'price' => $this->faker->randomFloat(2, 100, 1000),
            'term_months' => 12,
            'water_connections_number' => 100,
            'users_number' => 20,
        ];
    }
}
