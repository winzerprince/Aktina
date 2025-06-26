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
        'specifications' => 'array',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function boms()
    {
        return $this->belongsToMany(Bom::class, 'bom_resource');
    }

    public function isLowStock()
    {
        return $this->units <= $this->reorder_level;
    }

    public function isOverstock()
    {
        return $this->units >= $this->overstock_level;
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
