<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get all project milestones
     */
    public function projectMilestones()
    {
        return $this->hasMany(ProjectMilestone::class);
    }

    /**
     * Scope to search milestones
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%");
    }
}
