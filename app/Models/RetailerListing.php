<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetailerListing extends Model
{
    use HasFactory;

    protected $table = 'retailer_listing';

    protected $guarded = [
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function vendors()
    {
        return $this->hasMany(Vendor::class);
    }

    public function isApproved()
    {
        return $this->application && $this->application->isApproved();
    }

    public function getStatusAttribute()
    {
        return $this->application ? $this->application->status : 'pending';
    }
}
