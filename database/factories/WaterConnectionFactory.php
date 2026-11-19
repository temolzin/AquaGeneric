<?php

namespace Database\Factories;

use App\Models\Cost;
use App\Models\Customer;
use App\Models\Locality;
use App\Models\User;
use App\Models\WaterConnection;
use Illuminate\Database\Eloquent\Factories\Factory;

class WaterConnectionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WaterConnection::class;

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
            'customer_id' => Customer::factory(),
            'cost_id' => Cost::factory(),
            'section_id' => null,
            'name' => 'Toma '.$this->faker->unique()->numerify('#####'),
            'block' => $this->faker->numberBetween(1, 20),
            'street' => $this->faker->streetName(),
            'exterior_number' => $this->faker->buildingNumber(),
            'interior_number' => '-',
            'occupants_number' => $this->faker->numberBetween(1, 6),
            'water_days' => json_encode(['lunes', 'miércoles', 'viernes']),
            'has_water_pressure' => $this->faker->boolean(),
            'has_cistern' => $this->faker->boolean(),
            'type' => 'residencial',
            'note' => null,
        ];
    }

    /**
     * Indicate that the water connection is canceled.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function canceled()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_canceled' => true,
                'canceled_at' => now(),
                'cancel_description' => 'Corte por adeudo',
            ];
        });
    }
}
