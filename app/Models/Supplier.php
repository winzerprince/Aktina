<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier';

    protected $guarded = [
         // Keep for backward compatibility
    ];

    protected $casts = [
        'component_categories' => 'array',
        'resources' => 'array',
        'reliability_rating' => 'decimal:2',
        'is_preferred' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

    public function isPreferred()
    {
        return $this->is_preferred;
    }

    public function suppliesComponent($componentType)
    {
        return in_array($componentType, $this->component_categories ?? []);
    }

    public function getReliabilityStarsAttribute()
    {
        return str_repeat('★', floor($this->reliability_rating)) .
               str_repeat('☆', 5 - floor($this->reliability_rating));
    }

    public function isAsianPacific()
    {
        return $this->region === 'Asia-Pacific';
    }

    public function isUS()
    {
        return $this->region === 'US';
    }
}
