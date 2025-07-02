<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
    ];

    // Relationships
    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function movedBy()
    {
        return $this->belongsTo(User::class, 'moved_by');
    }

    // Helper methods
    public function getMovementDescriptionAttribute()
    {
        return match ($this->movement_type) {
            'inbound' => 'Received into ' . ($this->toWarehouse?->name ?? 'warehouse'),
            'outbound' => 'Shipped from ' . ($this->fromWarehouse?->name ?? 'warehouse'),
            'transfer' => 'Transferred from ' . ($this->fromWarehouse?->name ?? 'warehouse') . ' to ' . ($this->toWarehouse?->name ?? 'warehouse'),
            'adjustment' => 'Inventory adjustment',
            'production_use' => 'Used in production',
            'return' => 'Returned to inventory',
            default => 'Inventory movement',
        };
    }

    public function isInbound()
    {
        return $this->movement_type === 'inbound';
    }

    public function isOutbound()
    {
        return $this->movement_type === 'outbound';
    }

    public function isTransfer()
    {
        return $this->movement_type === 'transfer';
    }

    // Scopes
    public function scopeByResource($query, $resourceId)
    {
        return $query->where('resource_id', $resourceId);
    }

    public function scopeByWarehouse($query, $warehouseId)
    {
        return $query->where(function ($q) use ($warehouseId) {
            $q->where('from_warehouse_id', $warehouseId)
              ->orWhere('to_warehouse_id', $warehouseId);
        });
    }

    public function scopeByType($query, $type)
    {
        return $query->where('movement_type', $type);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
