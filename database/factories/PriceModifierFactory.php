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
        $from = $this->faker->dateTimeBetween('+5 days', '+2 months');
        $to = (clone $from)->modify(sprintf('+%s days', $this->faker->numberBetween(2, 8)));

        return [
            'from' => $from->setTime(0, 0),
            'to' => $to->setTime(0, 0),
            'type' => $this->faker->randomElement(['amount', 'percentage']),
            'value' => $this->faker->numberBetween(-10, 10),
        ];
    }
}
