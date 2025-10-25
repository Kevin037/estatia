<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormulaDetail extends Model
{
    protected $fillable = [
        'formula_id',
        'material_id',
        'qty',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
    ];

    public function formula()
    {
        return $this->belongsTo(Formula::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
