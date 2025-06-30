<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResourceOrder extends Model
{
    use HasFactory;

    protected $table = 'resource_orders';

    protected $guarded = [];

    protected $casts = [
        'price' => 'decimal:2',
        'items' => 'array',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_COMPLETE = 'complete';

    /**
     * Get the Aktina company as buyer.
     */
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Get the supplier as seller.
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Get the total number of resource items in this order.
     */
    public function getTotalItemsAttribute()
    {
        $items = $this->getItemsAsArray();
        return collect($items)->sum('quantity');
    }

    /**
     * Get the count of unique resource items in this order.
     */
    public function getItemsCountAttribute()
    {
        $items = $this->getItemsAsArray();
        return count($items);
    }

    /**
     * Helper method to ensure items is always an array.
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

    /**
     * Check if any resources in the order are low on stock.
     */
    public function hasLowStockResources()
    {
        $items = $this->getItemsAsArray();
        foreach ($items as $item) {
            $resource = Resource::find($item['resource_id'] ?? null);
            if ($resource && $resource->isLowStock()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get an array of resources included in this order with their details.
     */
    public function getResourcesDetails()
    {
        $details = [];
        $items = $this->getItemsAsArray();

        foreach ($items as $item) {
            $resource = Resource::find($item['resource_id'] ?? null);
            if ($resource) {
                $details[] = [
                    'resource' => $resource,
                    'quantity' => $item['quantity'] ?? 0,
                    'low_stock' => $resource->isLowStock(),
                ];
            }
        }

        return $details;
    }
}
