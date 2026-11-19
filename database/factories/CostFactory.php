<?php

namespace Database\Factories;

use App\Models\Cost;
use App\Models\Locality;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Cost::class;

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
            'category' => 'Servicio de Agua',
            'price' => $this->faker->randomFloat(2, 50, 500),
            'description' => 'Cuota mensual de agua',
        ];
    }
}
