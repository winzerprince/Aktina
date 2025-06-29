<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'product';

    protected $fillable = [
        'name',
        'model',
        'sku',
        'description',
        'msrp',
        'category',
        'specifications',
        'target_market',
        'bom_id',
    ];

    protected $casts = [
        'msrp' => 'decimal:2',
        'specifications' => 'array',
    ];

    public function bom()
    {
        return $this->belongsTo(Bom::class);
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
