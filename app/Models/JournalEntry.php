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
