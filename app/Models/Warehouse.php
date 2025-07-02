<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'address' => 'array',
        'capacity_utilization' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

    public function inventoryAlerts()
    {
        return $this->hasMany(InventoryAlert::class);
    }

    public function outgoingMovements()
    {
        return $this->hasMany(InventoryMovement::class, 'from_warehouse_id');
    }

    public function incomingMovements()
    {
        return $this->hasMany(InventoryMovement::class, 'to_warehouse_id');
    }

    // Helper methods
    public function updateCapacityUtilization()
    {
        if ($this->total_capacity > 0) {
            $this->capacity_utilization = ($this->current_usage / $this->total_capacity) * 100;
            $this->save();
        }
    }

    public function isNearCapacity($threshold = 90)
    {
        return $this->capacity_utilization >= $threshold;
    }

    public function canAccommodate($quantity)
    {
        return ($this->current_usage + $quantity) <= $this->total_capacity;
    }

    public function getAvailableCapacityAttribute()
    {
        return $this->total_capacity - $this->current_usage;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
