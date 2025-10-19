<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contractor extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get all project contractors
     */
    public function projectContractors()
    {
        return $this->hasMany(ProjectContractor::class);
    }

    /**
     * Get all projects through project contractors
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_contractors');
    }

    /**
     * Scope to search contractors
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }
}
