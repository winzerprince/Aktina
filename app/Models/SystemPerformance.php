<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemPerformance extends Model
{
    use HasFactory;

    protected $table = 'system_performances';

    protected $fillable = [
        'cpu_usage',
        'memory_usage',
        'disk_usage',
        'response_time',
        'has_alerts',
        'alert_messages',
    ];

    protected $casts = [
        'cpu_usage' => 'float',
        'memory_usage' => 'float',
        'disk_usage' => 'float',
        'response_time' => 'float',
        'has_alerts' => 'boolean',
        'alert_messages' => 'array',
    ];

    /**
     * Scope a query to only include records with alerts.
     */
    public function scopeWithAlerts($query)
    {
        return $query->where('has_alerts', true);
    }

    /**
     * Get critical system performances with alerts in the past 24 hours
     */
    public static function getRecentCritical()
    {
        return static::withAlerts()
                    ->where('created_at', '>=', now()->subDay())
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    /**
     * Check if CPU is overloaded
     */
    public function isCpuOverloaded(): bool
    {
        return $this->cpu_usage > 80;
    }

    /**
     * Check if memory is overloaded
     */
    public function isMemoryOverloaded(): bool
    {
        return $this->memory_usage > 85;
    }

    /**
     * Check if disk space is critical
     */
    public function isDiskCritical(): bool
    {
        return $this->disk_usage > 90;
    }

    /**
     * Check if response time is slow
     */
    public function isResponseTimeSlow(): bool
    {
        return $this->response_time > 2000; // 2 seconds
    }
}
