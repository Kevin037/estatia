<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get all units for this sales
     */
    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    /**
     * Scope to search sales
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    /**
     * Get sales with units count
     */
    public static function withUnitsCount()
    {
        return static::withCount('units');
    }
}
