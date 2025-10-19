<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get the order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get all payments
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Scope to search invoices
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
     * Get invoices with related data
     */
    public static function withRelations()
    {
        return static::with(['order.customer', 'payments']);
    }

    /**
     * Generate invoice number
     */
    public static function generateNumber()
    {
        $lastInvoice = static::orderBy('id', 'desc')->first();
        $number = $lastInvoice ? (int)substr($lastInvoice->no, 4) + 1 : 1;
        return 'INV-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate total paid amount
     */
    public function getTotalPaidAttribute()
    {
        return $this->payments()->sum('amount');
    }
}
