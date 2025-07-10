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
        'fulfillment_data' => 'array',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'fulfillment_started_at' => 'datetime',
        'shipped_at' => 'datetime',
        'completed_at' => 'datetime',
        'estimated_delivery' => 'datetime',
        'expected_delivery_date' => 'datetime',
        'fulfillment_failed_at' => 'datetime',
        'is_backorder' => 'boolean',
    ];

    // Order status constants
    public const STATUS_PENDING = 'pending';             // Initial state when order is created
    public const STATUS_ACCEPTED = 'accepted';           // Order accepted by seller
    public const STATUS_REJECTED = 'rejected';           // Order rejected by seller
    public const STATUS_PROCESSING = 'processing';       // Order is being processed/prepared
    public const STATUS_PARTIALLY_FULFILLED = 'partially_fulfilled'; // Some items fulfilled
    public const STATUS_FULFILLED = 'fulfilled';         // All items prepared for shipping
    public const STATUS_SHIPPED = 'shipped';             // Order has been shipped
    public const STATUS_IN_TRANSIT = 'in_transit';       // Order is on the way
    public const STATUS_DELIVERED = 'delivered';         // Order has been delivered
    public const STATUS_COMPLETE = 'complete';           // Order process is complete
    public const STATUS_CANCELLED = 'cancelled';         // Order was cancelled
    public const STATUS_RETURNED = 'returned';           // Order was returned
    public const STATUS_FULFILLMENT_FAILED = 'fulfillment_failed'; // Fulfillment process failed

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function approver(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function assignedWarehouse(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Warehouse::class, 'assigned_warehouse_id');
    }

    public function parentOrder(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class, 'parent_order_id');
    }

    public function childOrders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class, 'parent_order_id');
    }

    /**
     * Get the employees assigned to this order.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
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

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_ACCEPTED => 'blue',
            self::STATUS_PROCESSING => 'sky',
            self::STATUS_PARTIALLY_FULFILLED => 'orange',
            self::STATUS_FULFILLED => 'cyan',
            self::STATUS_SHIPPED, self::STATUS_IN_TRANSIT => 'indigo',
            self::STATUS_DELIVERED => 'emerald',
            self::STATUS_COMPLETE => 'green',
            self::STATUS_REJECTED, self::STATUS_FULFILLMENT_FAILED => 'red',
            self::STATUS_CANCELLED => 'rose',
            self::STATUS_RETURNED => 'purple',
            default => 'gray'
        };
    }

    public function getStatusColor(): string
    {
        return $this->getStatusColorAttribute();
    }

    public function getFormattedStatusAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    public function isEditable(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING]);
    }

    public function canBeApproved(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canBeRejected(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_ACCEPTED,
            self::STATUS_PROCESSING
        ]);
    }

    public function canBeProcessed(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    public function canBeFulfilled(): bool
    {
        return in_array($this->status, [
            self::STATUS_ACCEPTED,
            self::STATUS_PROCESSING,
            self::STATUS_PARTIALLY_FULFILLED
        ]);
    }

    public function canBeShipped(): bool
    {
        return in_array($this->status, [
            self::STATUS_FULFILLED,
            self::STATUS_PARTIALLY_FULFILLED
        ]);
    }

    public function canBeDelivered(): bool
    {
        return in_array($this->status, [
            self::STATUS_SHIPPED,
            self::STATUS_IN_TRANSIT
        ]);
    }

    public function canBeCompleted(): bool
    {
        return in_array($this->status, [
            self::STATUS_ACCEPTED,
            self::STATUS_PROCESSING,
            self::STATUS_FULFILLED,
            self::STATUS_SHIPPED,
            self::STATUS_DELIVERED
        ]);
    }

    public function canBeReturned(): bool
    {
        return in_array($this->status, [
            self::STATUS_DELIVERED,
            self::STATUS_COMPLETE
        ]);
    }

    public function getTotalAmountAttribute(): float
    {
        return $this->price;
    }
}
