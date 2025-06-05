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
        $fruitSeed = [
            ['id' => 1, 'name' => 'Naga', 'stock_in_kg' => 50, 'price_per_kg' => 40000],
            ['id' => 2, 'name' => 'Apel', 'stock_in_kg' => 20, 'price_per_kg' => 30000],
            ['id' => 3, 'name' => 'Anggur', 'stock_in_kg' => 100, 'price_per_kg' => 50000],
        ];
        
        $fruitId = $this->faker->numberBetween(1, 3);
        $pricePerKg = collect($fruitSeed)->firstWhere('id', $fruitId)['price_per_kg'] ?? 0;
        $requestedStockInKg = $this->faker->numberBetween(50, 200);
        $totalPrice = $requestedStockInKg * $pricePerKg;
        $requestedDate = $this->faker->dateTimeBetween('-18 months', 'now');
        $acceptedDate = $this->faker->dateTimeBetween($requestedDate, $requestedDate->modify('+7 days'));

        return [
            'requested_stock_in_kg' => $requestedStockInKg,
            'status_id' => 2, // accepted
            'distributor_id' => $this->faker->numberBetween(1, 5),
            'total_price' => $totalPrice,
            'requested_date' => $requestedDate,
            'status_changed_date' => $acceptedDate,
            'fruit_id' => $fruitId
        ];
    }
}
