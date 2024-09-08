<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Apartment>
 */
class ApartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'bedrooms' => $this->faker->numberBetween(1, 5),
            'bathrooms' => $this->faker->numberBetween(1, 3),
            'guests' => $this->faker->numberBetween(1, 7),
            'pets_allowed' => (bool)$this->faker->numberBetween(0, 1),
            'location_lat' => $this->faker->latitude(),
            'location_lon' => $this->faker->longitude(),
        ];
    }
}
