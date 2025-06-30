<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $guarded = [
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'items' => 'array',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_COMPLETE = 'complete';

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function getTotalItemsAttribute()
    {
        $items = $this->getItemsAsArray();
        return collect($items)->sum('quantity');
    }

    public function getItemsCountAttribute()
    {
        $items = $this->getItemsAsArray();
        return count($items);
    }

    /**
     * Helper method to ensure items is always an array
     */
    public function getItemsAsArray()
    {
        if (is_array($this->items)) {
            return $this->items;
        }

        if (is_string($this->items)) {
            $decoded = json_decode($this->items, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    public function getOrderValueTierAttribute()
    {
        if ($this->price >= 50000) {
            return 'enterprise';
        } elseif ($this->price >= 10000) {
            return 'bulk';
        } elseif ($this->price >= 1000) {
            return 'retail';
        }
        return 'individual';
    }

    public function containsFlagshipPhones()
    {
        $items = $this->getItemsAsArray();
        foreach ($items as $item) {
            $product = Product::find($item['product_id'] ?? null);
            if ($product && $product->isFlagship()) {
                return true;
            }
        }
        return false;
    }

    public function getPhoneModelsAttribute()
    {
        $models = [];
        $items = $this->getItemsAsArray();
        foreach ($items as $item) {
            $product = Product::find($item['product_id'] ?? null);
            if ($product) {
                $models[] = $product->model;
            }
        }
        return array_unique($models);
    }
}
