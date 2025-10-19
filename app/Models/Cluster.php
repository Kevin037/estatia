<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cluster extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'road_width' => 'decimal:2',
    ];

    /**
     * Get the project
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get all units in this cluster
     */
    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    /**
     * Scope to search clusters
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    /**
     * Scope to filter by project
     */
    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    /**
     * Get clusters with units count
     */
    public static function withUnitsCount()
    {
        return static::withCount('units');
    }
}
