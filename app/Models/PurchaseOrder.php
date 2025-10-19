<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'dt' => 'date',
        'total' => 'decimal:2',
    ];

    /**
     * Get the project for this purchase order
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the supplier for this purchase order
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get all purchase order details
     */
    public function details()
    {
        return $this->hasMany(PurchaseOrderDetail::class);
    }

    /**
     * Scope to search purchase orders
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('no', 'like', "%{$search}%");
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('dt', [$startDate, $endDate]);
    }

    /**
     * Get purchase orders with related data
     */
    public static function withRelations()
    {
        return static::with(['project', 'supplier', 'details.material']);
    }

    /**
     * Generate purchase order number
     */
    public static function generateNumber()
    {
        $lastPO = static::orderBy('id', 'desc')->first();
        $number = $lastPO ? (int)substr($lastPO->no, 3) + 1 : 1;
        return 'PO-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
