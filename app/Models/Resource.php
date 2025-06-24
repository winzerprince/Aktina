<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $table = 'resource';

    protected $fillable = [
        'name',
        'component_type',
        'category',
        'units',
        'reorder_level',
        'overstock_level',
        'unit_cost',
        'part_number',
        'specifications',
        'description',
        'supplier_id',
        'bom_id',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'specifications' => 'array',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function bom()
    {
        return $this->belongsTo(Bom::class);
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
