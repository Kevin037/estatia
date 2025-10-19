<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'land_area' => 'decimal:2',
        'building_area' => 'decimal:2',
    ];

    /**
     * Get all products of this type
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Scope to search types
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }
}
