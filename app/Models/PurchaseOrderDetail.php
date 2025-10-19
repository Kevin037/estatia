<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderDetail extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'qty' => 'decimal:2',
    ];

    /**
     * Get the purchase order
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * Get the material
     */
    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    /**
     * Get subtotal amount
     */
    public function getSubtotalAttribute()
    {
        return $this->qty * $this->material->price;
    }
}
