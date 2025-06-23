<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $table = 'application';

    protected $fillable = [
        'status',
        'meeting_schedule',
        'vendor_id',
    ];

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
}
