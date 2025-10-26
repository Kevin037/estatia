<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'dt' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    /**
     * Get the account
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the related transaction dynamically based on transaction_name and transaction_id
     * 
     * Example: 
     * - If transaction_name = 'Order', it will return the Order model
     * - If transaction_name = 'PurchaseOrder', it will return the PurchaseOrder model
     * 
     * Usage: $journalEntry->transaction()
     */
    public function transaction()
    {
        if (!$this->transaction_name || !$this->transaction_id) {
            return null;
        }

        // Convert transaction_name to the model class
        // e.g., 'Order' -> App\Models\Order
        // e.g., 'PurchaseOrder' -> App\Models\PurchaseOrder
        $modelClass = "App\\Models\\" . $this->transaction_name;

        // Check if the model class exists
        if (!class_exists($modelClass)) {
            return null;
        }

        // Return the related model instance
        return $modelClass::find($this->transaction_id);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('dt', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by account
     */
    public function scopeByAccount($query, $accountId)
    {
        return $query->where('account_id', $accountId);
    }

    /**
     * Scope to filter by transaction
     */
    public function scopeByTransaction($query, $transactionId, $transactionName)
    {
        return $query->where('transaction_id', $transactionId)
                     ->where('transaction_name', $transactionName);
    }

    /**
     * Get journal entries with account info
     */
    public static function withAccount()
    {
        return static::with('account');
    }
}
