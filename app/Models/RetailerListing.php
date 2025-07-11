<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetailerListing extends Model
{
    use HasFactory;

    protected $table = 'retailer_listings';

    protected $guarded = [
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function retailer()
    {
        return $this->belongsTo(User::class, 'retailer_id');
    }

    public function isApproved()
    {
        return $this->application && $this->application->isApproved();
    }

    public function getStatusAttribute()
    {
        return $this->application ? $this->application->status : 'pending';
    }

    public function getRetailerNameAttribute()
    {
        return $this->retailer ? $this->retailer->name : $this->attributes['retailer_name'] ?? null;
    }

    public function getRetailerEmailAttribute()
    {
        return $this->retailer ? $this->retailer->email : $this->attributes['retailer_email'] ?? null;
    }
}
