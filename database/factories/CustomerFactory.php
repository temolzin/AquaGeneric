<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Locality;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'locality_id' => Locality::factory(),
            'created_by' => User::factory(),
            'name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'locality' => $this->faker->city(),
            'state' => $this->faker->state(),
            'zip_code' => $this->faker->numerify('#####'),
            'block' => $this->faker->numberBetween(1, 20),
            'street' => $this->faker->streetName(),
            'exterior_number' => $this->faker->buildingNumber(),
            'interior_number' => '-',
            'marital_status' => $this->faker->boolean(),
            'status' => true,
            'responsible_name' => null,
            'note' => null,
            'user_id' => null,
        ];
    }
}
