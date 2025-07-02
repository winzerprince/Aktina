<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryAlert extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    // Relationships
    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    // Helper methods
    public function resolve(User $user, $notes = null)
    {
        $this->update([
            'is_resolved' => true,
            'resolved_at' => now(),
            'resolved_by' => $user->id,
            'notes' => $notes,
        ]);
    }

    public function getStatusAttribute()
    {
        if ($this->is_resolved) {
            return 'resolved';
        }
        return $this->is_active ? 'active' : 'inactive';
    }

    public function getPriorityAttribute()
    {
        return match ($this->alert_type) {
            'critical' => 1,
            'low_stock' => 2,
            'overstock' => 3,
            'expired' => 2,
            default => 4,
        };
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('is_resolved', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('alert_type', $type);
    }

    public function scopeCritical($query)
    {
        return $query->where('alert_type', 'critical');
    }

    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }
}
