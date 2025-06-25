<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    use HasFactory;

    protected $table = 'production';

    protected $guarded = [
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getRemainingUnitsAttribute()
    {
        return $this->units - $this->completed_units - $this->cancelled_units;
    }

    public function getCompletionPercentageAttribute()
    {
        return $this->units > 0 ? ($this->completed_units / $this->units) * 100 : 0;
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    public function getEstimatedCompletionDateAttribute()
    {
        if ($this->isCompleted() || $this->remaining_units <= 0) {
            return null;
        }

        // Estimate based on current progress rate
        $daysRunning = $this->created_at->diffInDays(now());
        if ($daysRunning > 0 && $this->completed_units > 0) {
            $unitsPerDay = $this->completed_units / $daysRunning;
            $daysRemaining = $this->remaining_units / $unitsPerDay;
            return now()->addDays(ceil($daysRemaining));
        }

        return null;
    }

    public function isPhoneFlagship()
    {
        return $this->product && $this->product->isFlagship();
    }

    public function getAssemblyLineTypeAttribute()
    {
        if (str_contains($this->assembly_line, 'Flagship')) {
            return 'flagship';
        } elseif (str_contains($this->assembly_line, 'Mid-Range')) {
            return 'mid-range';
        } elseif (str_contains($this->assembly_line, 'Budget')) {
            return 'budget';
        }
        return 'standard';
    }
}
