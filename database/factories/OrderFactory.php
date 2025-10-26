<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Cluster;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $unit = Unit::inRandomOrder()->first() ?? Unit::factory()->create();
        
        return [
            'no' => 'ORD-' . fake()->unique()->numerify('######'),
            'dt' => fake()->dateTimeBetween('-1 year', 'now'),
            'customer_id' => Customer::factory(),
            'project_id' => $unit->cluster->project_id,
            'cluster_id' => $unit->cluster_id,
            'unit_id' => $unit->id,
            'total' => $unit->price,
            'status' => fake()->randomElement(['pending', 'completed']),
            'notes' => fake()->optional(0.6)->sentence(),
        ];
    }

    /**
     * Indicate that the order is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    /**
     * Indicate that the order is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }
}
