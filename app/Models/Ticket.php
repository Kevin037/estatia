<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'dt' => 'date',
    ];

    /**
     * Get the order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Scope to search tickets
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('no', 'like', "%{$search}%")
              ->orWhere('title', 'like', "%{$search}%");
        });
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
     * Generate ticket number
     */
    public static function generateNumber()
    {
        $lastTicket = static::orderBy('id', 'desc')->first();
        $number = $lastTicket ? (int)substr($lastTicket->no, 4) + 1 : 1;
        return 'TKT-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
