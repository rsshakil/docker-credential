<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->numerify('コイン ###'),
            'unit' => 'Game$',
            'scale' => 'Game$',
            'currency_id' => 'Game$',
            'base_currency_rate' => 150,
            'trade_fee' => 150,
            'refund_fee' => 2,
        ];
    }
}
