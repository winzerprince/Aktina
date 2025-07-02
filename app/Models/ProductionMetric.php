<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionMetric extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'efficiency_rate' => 'decimal:2',
        'fulfillment_rate' => 'decimal:2',
        'resource_usage' => 'decimal:2',
        'waste_percentage' => 'decimal:2',
        'quality_score' => 'decimal:2',
        'cost_per_unit' => 'decimal:2',
        'resource_breakdown' => 'array',
        'production_lines' => 'array',
    ];

    // Scopes
    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeHighEfficiency($query, $threshold = 80)
    {
        return $query->where('efficiency_rate', '>', $threshold);
    }

    // Helper methods
    public function getOverallPerformanceScore()
    {
        return ($this->efficiency_rate + $this->fulfillment_rate + $this->quality_score) / 3;
    }

    public function isPerformingWell($threshold = 75)
    {
        return $this->getOverallPerformanceScore() >= $threshold;
    }

    public function getEfficiencyTrend($previousPeriod)
    {
        if ($previousPeriod) {
            return $this->efficiency_rate - $previousPeriod->efficiency_rate;
        }
        return 0;
    }

    public static function calculateForDate($date)
    {
        $productions = Production::whereDate('created_at', $date)->get();
        
        $unitsProduced = $productions->sum('quantity_produced');
        $unitsPlanned = $productions->sum('quantity_planned');
        $efficiencyRate = $unitsPlanned > 0 ? ($unitsProduced / $unitsPlanned) * 100 : 0;

        return static::updateOrCreate([
            'date' => $date,
        ], [
            'units_produced' => $unitsProduced,
            'units_planned' => $unitsPlanned,
            'efficiency_rate' => $efficiencyRate,
            // Additional calculations would go here
        ]);
    }

    public function getProductionTrends($days = 7)
    {
        return static::where('date', '>=', $this->date->subDays($days))
                    ->orderBy('date')
                    ->get(['date', 'efficiency_rate', 'fulfillment_rate', 'quality_score']);
    }
}
