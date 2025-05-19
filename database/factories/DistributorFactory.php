<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Distributor>
 */
class DistributorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'address' => $this->faker->address(),
            'email' => $this->faker->companyEmail(),
            'phone_number' => $this->faker->phoneNumber(),
            'username' => $this->faker->userName(),
            'password_hash' => password_hash('rahasia', PASSWORD_DEFAULT),
            'last_access' => now()
        ];
    }
}
