<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Land extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'wide' => 'decimal:2',
        'length' => 'decimal:2',
    ];

    /**
     * Get all projects on this land
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Scope to search lands
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('address', 'like', "%{$search}%")
              ->orWhere('location', 'like', "%{$search}%");
        });
    }

    /**
     * Get total area
     */
    public function getTotalAreaAttribute()
    {
        return $this->wide * $this->length;
    }
}
