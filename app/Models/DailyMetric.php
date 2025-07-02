<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyMetric extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'value' => 'decimal:2',
        'metadata' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeForRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeForMetricType($query, $type)
    {
        return $query->where('metric_type', $type);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    // Helper methods
    public static function recordMetric($date, $role, $metricType, $metricName, $value, $options = [])
    {
        return static::updateOrCreate([
            'date' => $date,
            'role' => $role,
            'metric_type' => $metricType,
            'metric_name' => $metricName,
            'user_id' => $options['user_id'] ?? null,
        ], [
            'value' => $value,
            'unit' => $options['unit'] ?? null,
            'metadata' => $options['metadata'] ?? null,
        ]);
    }

    public function getFormattedValueAttribute()
    {
        if ($this->unit === 'currency') {
            return '$' . number_format($this->value, 2);
        } elseif ($this->unit === 'percentage') {
            return number_format($this->value, 2) . '%';
        }
        return number_format($this->value);
    }
}
