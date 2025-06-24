<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bom extends Model
{
    use HasFactory;

    protected $table = 'bom';

    protected $fillable = [
        'price',
        'product_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

    public function getTotalComponentCostAttribute()
    {
        return $this->resources()->sum('unit_cost');
    }

    public function getComponentCountAttribute()
    {
        return $this->resources()->count();
    }

    public function getProfitMarginAttribute()
    {
        if (!$this->product || !$this->product->msrp) {
            return null;
        }
        return (($this->product->msrp - $this->price) / $this->product->msrp) * 100;
    }
}
