<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $table = 'application';

    protected $guarded = [];

    protected $casts = [
        'meeting_schedule' => 'date',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function retailerListings()
    {
        return $this->hasMany(RetailerListing::class);
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isPartiallyApproved()
    {
        return $this->status === 'partially approved';
    }

    public function getDaysUntilMeetingAttribute()
    {
        if (!$this->meeting_schedule) {
            return null;
        }
        return now()->diffInDays($this->meeting_schedule, false);
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'approved' => 'green',
            'partially approved' => 'yellow',
            'pending' => 'blue',
            'rejected' => 'red',
            default => 'gray'
        };
    }
}
