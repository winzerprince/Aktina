<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $guarded = [
    ];

    protected $casts = [
        'msrp' => 'decimal:2',
        'specifications' => 'array',
        'company_quantities' => 'array',
    ];

    public function inventoryItems()
    {
        // Get resources through BOM relationship
        if (!$this->bom) {
            return collect();
        }

        return $this->bom->resources;
    }

    public function getTotalQuantityAttribute()
    {
        if (!$this->company_quantities) {
            return 0;
        }

        return collect($this->company_quantities)->sum('quantity');
    }

    public function getTotalQuantity()
    {
        if (!$this->company_quantities) {
            return 0;
        }

        return collect($this->company_quantities)->sum('quantity');
    }

    public function getCompanyQuantity($companyName)
    {
        return $this->company_quantities[$companyName]['quantity'] ?? 0;
    }

    public function setCompanyQuantity($companyName, $quantity)
    {
        $quantities = $this->company_quantities ?? [];
        $quantities[$companyName] = [
            'quantity' => $quantity,
            'updated_at' => now()->toISOString()
        ];
        $this->company_quantities = $quantities;
    }

    public function bom()
    {
        return $this->hasOne(Bom::class);
    }

    public function productions()
    {
        return $this->hasMany(Production::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function averageRating()
    {
        return $this->ratings()->avg('rating');
    }

    public function isFlagship()
    {
        return $this->target_market === 'flagship';
    }

    public function isMidRange()
    {
        return $this->target_market === 'mid-range';
    }

    public function isBudget()
    {
        return $this->target_market === 'budget';
    }

    public function getDisplaySizeAttribute()
    {
        return $this->specifications['display'] ?? null;
    }

    public function getProcessorAttribute()
    {
        return $this->specifications['processor'] ?? null;
    }

    public function getRamAttribute()
    {
        return $this->specifications['ram'] ?? null;
    }

    public function getStorageAttribute()
    {
        return $this->specifications['storage'] ?? null;
    }
}
