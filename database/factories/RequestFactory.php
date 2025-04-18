<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Request>
 */
class RequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'requested_stock_in_kg' => $this->faker->numberBetween(50, 200),
            'total_price' => $this->faker->numberBetween(2500000, 10000000),
            'requested_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'fruit_id' => $this->faker->numberBetween(1, 3)
        ];
    }
}
