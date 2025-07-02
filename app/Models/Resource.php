<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $table = 'resources';

    protected $guarded = [
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'average_cost' => 'decimal:2',
        'specifications' => 'array',
        'last_movement_at' => 'datetime',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function inventoryAlerts()
    {
        return $this->hasMany(InventoryAlert::class);
    }

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function boms()
    {
        return $this->belongsToMany(Bom::class, 'bom_resource');
    }

    public function isLowStock()
    {
        return $this->available_quantity <= $this->reorder_level;
    }

    public function isOverstock()
    {
        return $this->units >= $this->overstock_level;
    }

    public function isCriticalStock()
    {
        return $this->available_quantity <= ($this->reorder_level * 0.5);
    }

    public function updateAvailableQuantity()
    {
        $this->available_quantity = max(0, $this->units - $this->reserved_quantity);
        $this->save();
    }

    public function reserveQuantity($quantity)
    {
        if ($this->available_quantity >= $quantity) {
            $this->reserved_quantity += $quantity;
            $this->updateAvailableQuantity();
            return true;
        }
        return false;
    }

    public function releaseReservedQuantity($quantity)
    {
        $this->reserved_quantity = max(0, $this->reserved_quantity - $quantity);
        $this->updateAvailableQuantity();
    }

    public function recordMovement($movementType, $quantity, $user, $options = [])
    {
        $beforeQuantity = $this->units;
        
        // Update stock based on movement type
        switch ($movementType) {
            case 'inbound':
                $this->units += $quantity;
                break;
            case 'outbound':
            case 'production_use':
                $this->units = max(0, $this->units - $quantity);
                break;
            case 'adjustment':
                $this->units = $quantity; // Direct adjustment
                break;
        }
        
        $this->last_movement_at = now();
        $this->updateAvailableQuantity();
        
        // Create movement record
        $this->inventoryMovements()->create([
            'movement_type' => $movementType,
            'quantity' => $quantity,
            'before_quantity' => $beforeQuantity,
            'after_quantity' => $this->units,
            'from_warehouse_id' => $options['from_warehouse_id'] ?? null,
            'to_warehouse_id' => $options['to_warehouse_id'] ?? $this->warehouse_id,
            'reference_number' => $options['reference_number'] ?? null,
            'moved_by' => $user->id,
            'reason' => $options['reason'] ?? null,
            'metadata' => $options['metadata'] ?? null,
        ]);
        
        // Check for alerts
        $this->checkAndCreateAlerts();
    }

    public function checkAndCreateAlerts()
    {
        // Check for low stock alert
        if ($this->isLowStock()) {
            $this->createAlert('low_stock', $this->reorder_level, $this->available_quantity);
        }
        
        // Check for critical stock alert
        if ($this->isCriticalStock()) {
            $this->createAlert('critical', $this->reorder_level * 0.5, $this->available_quantity);
        }
        
        // Check for overstock alert
        if ($this->isOverstock()) {
            $this->createAlert('overstock', $this->overstock_level, $this->units);
        }
    }

    private function createAlert($alertType, $threshold, $currentValue)
    {
        // Only create if no active alert of this type exists
        $existingAlert = $this->inventoryAlerts()
            ->where('alert_type', $alertType)
            ->where('is_active', true)
            ->where('is_resolved', false)
            ->first();
            
        if (!$existingAlert) {
            $this->inventoryAlerts()->create([
                'alert_type' => $alertType,
                'threshold_value' => $threshold,
                'current_value' => $currentValue,
                'warehouse_id' => $this->warehouse_id,
                'is_active' => true,
            ]);
        }
    }

    public function getSpecificationAttribute($key)
    {
        return $this->specifications[$key] ?? null;
    }

    public function isPhoneComponent()
    {
        return in_array($this->component_type, ['SoC', 'Display', 'Camera Sensor', 'Battery', 'Memory']);
    }
}
