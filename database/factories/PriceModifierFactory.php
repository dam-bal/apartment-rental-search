<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PriceModifier>
 */
class PriceModifierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $from = $this->faker->dateTimeBetween('+2 weeks', '+3 months');
        $to = (clone $from)->modify(sprintf('+%s days', $this->faker->numberBetween(2, 7)));

        return [
            'from' => $from,
            'to' => $to,
            'type' => $this->faker->randomElement(['amount', 'percentage']),
            'value' => $this->faker->numberBetween(-20, 20),
        ];
    }
}
