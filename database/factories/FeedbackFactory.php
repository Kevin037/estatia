<?php

namespace Database\Factories;

use App\Models\Feedback;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Feedback>
 */
class FeedbackFactory extends Factory
{
    protected $model = Feedback::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $feedbacks = [
            'Very satisfied with the unit quality and finishing. The location is strategic and the facilities are complete.',
            'The service from sales team was excellent. They were very responsive and helpful throughout the process.',
            'Good property with reasonable price. The neighborhood is quiet and safe for family.',
            'Love the design and layout of the unit. Modern and functional. Highly recommended!',
            'The construction quality is good. All utilities working properly. Happy with the purchase.',
            'Great investment opportunity. The developer is reliable and professional.',
            'Excellent location near schools, malls, and public transportation. Very convenient.',
            'The cluster facilities are well-maintained. Security is top-notch.',
            'Beautiful landscape and environment. Perfect for raising children.',
            'Smooth transaction process. Documentation and handover were handled professionally.',
        ];

        return [
            'order_id' => Order::factory(),
            'desc' => fake()->randomElement($feedbacks),
            'dt' => fake()->dateTimeBetween('-1 year', 'now'),
            'photo' => null, // We'll skip photo uploads for seeding
        ];
    }
}
