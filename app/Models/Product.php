<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'price' => 'decimal:2',
        'qty' => 'decimal:2',
    ];

    protected $appends = ['photo_url'];

    /**
     * Get the photo URL attribute
     */
    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return asset('images/no-image.png');
    }

    /**
     * Get the formula
     */
    public function formula()
    {
        return $this->belongsTo(Formula::class);
    }

    /**
     * Get the type
     */
    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    /**
     * Get all product photos
     */
    public function productPhotos()
    {
        return $this->hasMany(ProductPhoto::class);
    }

    /**
     * Get all units for this product
     */
    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    /**
     * Scope to search products
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('code', 'like', "%{$search}%");
    }

    /**
     * Scope to filter by type
     */
    public function scopeByType($query, $typeId)
    {
        return $query->where('type_id', $typeId);
    }

    /**
     * Get products with related data
     */
    public static function withRelations()
    {
        return static::with(['type', 'productPhotos']);
    }
}
