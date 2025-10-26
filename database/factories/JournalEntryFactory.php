<?php

namespace Database\Factories;

use App\Models\JournalEntry;
use App\Models\Account;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JournalEntry>
 */
class JournalEntryFactory extends Factory
{
    protected $model = JournalEntry::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get a random transaction
        $transactionTypes = ['Order', 'Payment', 'PurchaseOrder'];
        $transactionName = fake()->randomElement($transactionTypes);
        
        // Get a random transaction ID based on type
        $transactionId = match($transactionName) {
            'Order' => Order::inRandomOrder()->first()->id ?? 1,
            'Payment' => Payment::inRandomOrder()->first()->id ?? 1,
            'PurchaseOrder' => PurchaseOrder::inRandomOrder()->first()->id ?? 1,
            default => 1
        };

        // Random account
        $account = Account::where('parent_id', '!=', null)->inRandomOrder()->first();
        
        // Random amount
        $amount = fake()->numberBetween(100000, 50000000);
        
        // Randomly assign to debit or credit (we'll create pairs separately)
        $isDebit = fake()->boolean();

        return [
            'transaction_id' => $transactionId,
            'transaction_name' => $transactionName,
            'dt' => fake()->dateTimeBetween('-1 year', 'now'),
            'account_id' => $account->id ?? 1,
            'debit' => $isDebit ? $amount : 0,
            'credit' => $isDebit ? 0 : $amount,
            'desc' => fake()->sentence(),
            'journal_entry_id' => 0, // Will be set when creating pairs
        ];
    }

    /**
     * Create a balanced journal entry pair (debit + credit)
     */
    public function balanced(): static
    {
        return $this->afterCreating(function (JournalEntry $entry) {
            // Create the offsetting entry
            $oppositeEntry = JournalEntry::create([
                'transaction_id' => $entry->transaction_id,
                'transaction_name' => $entry->transaction_name,
                'dt' => $entry->dt,
                'account_id' => Account::where('id', '!=', $entry->account_id)
                    ->where('parent_id', '!=', null)
                    ->inRandomOrder()
                    ->first()->id ?? 1,
                'debit' => $entry->credit > 0 ? $entry->credit : 0,
                'credit' => $entry->debit > 0 ? $entry->debit : 0,
                'desc' => $entry->desc,
                'journal_entry_id' => $entry->id,
            ]);

            // Update the original entry with the pair ID
            $entry->update(['journal_entry_id' => $oppositeEntry->id]);
        });
    }
}
