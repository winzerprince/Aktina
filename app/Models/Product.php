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
        'sku',
        'description',
        'msrp',
        'bom_id',
    ];

    protected $casts = [
        'msrp' => 'decimal:2',
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
}
