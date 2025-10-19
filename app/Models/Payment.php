<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    /**
     * Get the invoice
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Scope to search payments
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('no', 'like', "%{$search}%");
    }

    /**
     * Scope to filter by payment type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('payment_type', $type);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('paid_at', [$startDate, $endDate]);
    }

    /**
     * Generate payment number
     */
    public static function generateNumber()
    {
        $lastPayment = static::orderBy('id', 'desc')->first();
        $number = $lastPayment ? (int)substr($lastPayment->no, 4) + 1 : 1;
        return 'PAY-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
