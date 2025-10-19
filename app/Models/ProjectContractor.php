<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectContractor extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get the project
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the contractor
     */
    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }
}
