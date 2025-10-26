<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $paymentType = fake()->randomElement(['cash', 'transfer']);
        $invoice = Invoice::inRandomOrder()->first() ?? Invoice::factory()->create();
        
        // Get partial or full amount
        $orderTotal = $invoice->order->total;
        $existingPayments = $invoice->payments()->sum('amount');
        $remaining = $orderTotal - $existingPayments;
        $amount = fake()->boolean(70) ? $remaining : fake()->numberBetween(1, $remaining);

        $data = [
            'no' => 'PAY-' . fake()->unique()->numerify('######'),
            'invoice_id' => $invoice->id,
            'dt' => fake()->dateTimeBetween($invoice->dt, 'now'),
            'amount' => $amount,
            'payment_type' => $paymentType,
            'paid_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ];

        // Add bank details only for transfer type
        if ($paymentType === 'transfer') {
            $banks = ['BCA', 'Mandiri', 'BNI', 'BRI', 'CIMB Niaga', 'Permata', 'OCBC NISP'];
            $data['bank_account_id'] = fake()->numerify('##########');
            $data['bank_account_name'] = fake()->name();
            $data['bank_account_type'] = fake()->randomElement($banks);
        }

        return $data;
    }

    /**
     * Indicate that the payment is cash.
     */
    public function cash(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_type' => 'cash',
            'bank_account_id' => null,
            'bank_account_name' => null,
            'bank_account_type' => null,
        ]);
    }

    /**
     * Indicate that the payment is transfer.
     */
    public function transfer(): static
    {
        $banks = ['BCA', 'Mandiri', 'BNI', 'BRI', 'CIMB Niaga'];
        
        return $this->state(fn (array $attributes) => [
            'payment_type' => 'transfer',
            'bank_account_id' => fake()->numerify('##########'),
            'bank_account_name' => fake()->name(),
            'bank_account_type' => fake()->randomElement($banks),
        ]);
    }
}
