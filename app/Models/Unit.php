<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the cluster
     */
    public function cluster()
    {
        return $this->belongsTo(Cluster::class);
    }

    /**
     * Get the sales
     */
    public function sales()
    {
        return $this->belongsTo(Sales::class);
    }

    /**
     * Get all unit photos
     */
    public function unitPhotos()
    {
        return $this->hasMany(UnitPhoto::class);
    }

    /**
     * Scope to search units
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('no', 'like', "%{$search}%");
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
     * Scope to filter by cluster
     */
    public function scopeByCluster($query, $clusterId)
    {
        return $query->where('cluster_id', $clusterId);
    }

    /**
     * Get units with related data
     */
    public static function withRelations()
    {
        return static::with(['product.type', 'cluster.project', 'sales', 'unitPhotos']);
    }
}
