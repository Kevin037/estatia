<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formula extends Model
{
    protected $fillable = [
        'code',
        'name',
        'total',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    public function details()
    {
        return $this->hasMany(FormulaDetail::class);
    }
}
