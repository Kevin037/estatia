<?php

namespace Database\Factories;

use App\Models\Unit;
use App\Models\Product;
use App\Models\Cluster;
use App\Models\Sales;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Unit>
 */
class UnitFactory extends Factory
{
    protected $model = Unit::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get random existing models
        $productId = \App\Models\Product::inRandomOrder()->first()->id ?? null;
        $clusterId = \App\Models\Cluster::inRandomOrder()->first()->id ?? null;
        $salesId = \App\Models\Sales::inRandomOrder()->first()->id ?? null;

        return [
            'name' => 'Unit ' . fake()->bothify('##-##'),
            'no' => fake()->unique()->numerify('###'),
            'price' => fake()->numberBetween(300000000, 2000000000), // 300M - 2B IDR
            'product_id' => $productId, // Will be overridden if passed explicitly
            'cluster_id' => $clusterId, // Will be overridden if passed explicitly
            'sales_id' => $salesId, // Will be overridden if passed explicitly
            'desc' => fake()->paragraph(2),
            'facilities' => implode(', ', fake()->randomElements([
                'AC', 'Water Heater', 'Kitchen Set', 'Carport',
                'Backyard', 'Balcony', 'Walk-in Closet', 'Maid Room'
            ], fake()->numberBetween(3, 5))),
            'status' => fake()->randomElement(['available', 'reserved', 'sold', 'handed_over']),
        ];
    }

    /**
     * Indicate that the unit is available.
     */
    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'available',
        ]);
    }

    /**
     * Indicate that the unit is sold.
     */
    public function sold(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sold',
        ]);
    }

    /**
     * Indicate that the unit is reserved.
     */
    public function reserved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'reserved',
        ]);
    }
}
