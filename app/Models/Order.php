<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'order';

    protected $fillable = [
        'price',
        'items',
        'buyer_id',
        'seller_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'items' => 'array',
    ];

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function getTotalItemsAttribute()
    {
        return collect($this->items)->sum('quantity');
    }

    public function getItemsCountAttribute()
    {
        return count($this->items);
    }
}
