<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $table = 'employees';

    protected $guarded = [];

    protected $casts = [
        'status' => 'string',
        'current_activity' => 'string',
    ];

    // Constants for status
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_UNAVAILABLE = 'unavailable';

    // Constants for activity
    public const ACTIVITY_ORDER = 'managing_order';
    public const ACTIVITY_PRODUCTION = 'managing_production';
    public const ACTIVITY_NONE = 'none';

    /**
     * Get the order this employee is managing.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the production this employee is managing.
     */
    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    /**
     * Check if employee is available for assignment.
     */
    public function isAvailable()
    {
        return $this->status === self::STATUS_AVAILABLE &&
               $this->current_activity === self::ACTIVITY_NONE;
    }

    /**
     * Assign employee to an order.
     */
    public function assignToOrder(Order $order)
    {
        $this->update([
            'status' => self::STATUS_UNAVAILABLE,
            'current_activity' => self::ACTIVITY_ORDER,
            'order_id' => $order->id,
            'production_id' => null
        ]);
    }

    /**
     * Assign employee to a production.
     */
    public function assignToProduction(Production $production)
    {
        $this->update([
            'status' => self::STATUS_UNAVAILABLE,
            'current_activity' => self::ACTIVITY_PRODUCTION,
            'production_id' => $production->id,
            'order_id' => null
        ]);
    }

    /**
     * Release employee from current assignment.
     */
    public function release()
    {
        $this->update([
            'status' => self::STATUS_AVAILABLE,
            'current_activity' => self::ACTIVITY_NONE,
            'order_id' => null,
            'production_id' => null
        ]);
    }
}
