<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get all orders for this customer
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Scope to search customers
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    /**
     * Get customer with orders count
     */
    public static function withOrdersCount()
    {
        return static::withCount('orders');
    }
}
