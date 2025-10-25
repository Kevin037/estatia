<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'desc',
    ];

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
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('desc', 'like', "%{$search}%");
        });
    }
}
