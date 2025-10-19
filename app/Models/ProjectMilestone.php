<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectMilestone extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'target_dt' => 'date',
        'completed_dt' => 'date',
    ];

    /**
     * Get the project
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the milestone
     */
    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by project
     */
    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    /**
     * Check if milestone is overdue
     */
    public function getIsOverdueAttribute()
    {
        return $this->status !== 'completed' && $this->target_dt < now();
    }
}
