<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'dt_start' => 'date',
        'dt_end' => 'date',
    ];

    /**
     * Get the land for this project
     */
    public function land()
    {
        return $this->belongsTo(Land::class);
    }

    /**
     * Get all clusters for this project
     */
    public function clusters()
    {
        return $this->hasMany(Cluster::class);
    }

    /**
     * Get all project contractors
     */
    public function projectContractors()
    {
        return $this->hasMany(ProjectContractor::class);
    }

    /**
     * Get all contractors through project contractors
     */
    public function contractors()
    {
        return $this->belongsToMany(Contractor::class, 'project_contractors');
    }

    /**
     * Get all project milestones
     */
    public function projectMilestones()
    {
        return $this->hasMany(ProjectMilestone::class);
    }

    /**
     * Get all purchase orders for this project
     */
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * Scope to search projects
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get projects with related data
     */
    public static function withRelations()
    {
        return static::with(['land', 'clusters', 'contractors']);
    }
}
