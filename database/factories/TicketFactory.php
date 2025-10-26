<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $issues = [
            'Unit Maintenance Issue',
            'Water Leakage Problem',
            'Electrical Issue',
            'Door/Window Repair',
            'Paint Touch-up Request',
            'Plumbing Problem',
            'AC Not Working',
            'Lighting Issue',
            'Wall Crack Repair',
            'Roof Leakage',
            'Floor Tile Issue',
            'Kitchen Set Problem',
            'Bathroom Fixture Issue',
            'Garden Maintenance',
            'Security Gate Problem'
        ];

        return [
            'no' => 'TIC-' . fake()->unique()->numerify('######'),
            'title' => fake()->randomElement($issues),
            'order_id' => Order::factory(),
            'desc' => fake()->paragraph(3),
            'dt' => fake()->dateTimeBetween('-6 months', 'now'),
            'photo' => null, // We'll skip photo uploads for seeding
            'status' => fake()->randomElement(['pending', 'completed']),
        ];
    }

    /**
     * Indicate that the ticket is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the ticket is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }
}
