<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'dt' => 'date',
        'total' => 'decimal:2',
    ];

    /**
     * Get the customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get all invoices
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get all tickets
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get all feedbacks
     */
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    /**
     * Scope to search orders
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('no', 'like', "%{$search}%");
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('dt', [$startDate, $endDate]);
    }

    /**
     * Get orders with related data
     */
    public static function withRelations()
    {
        return static::with(['customer', 'invoices', 'tickets', 'feedbacks']);
    }

    /**
     * Generate order number
     */
    public static function generateNumber()
    {
        $lastOrder = static::orderBy('id', 'desc')->first();
        $number = $lastOrder ? (int)substr($lastOrder->no, 4) + 1 : 1;
        return 'ORD-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
