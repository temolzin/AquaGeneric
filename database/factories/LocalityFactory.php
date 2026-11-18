<?php

namespace Database\Factories;

use App\Models\Locality;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocalityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Locality::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'Localidad '.$this->faker->unique()->city(),
            'municipality' => $this->faker->city(),
            'state' => $this->faker->state(),
            'zip_code' => $this->faker->numerify('#####'),
        ];
    }
}
