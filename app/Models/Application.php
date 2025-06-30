<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $table = 'applications';

    protected $guarded = [];

    protected $casts = [
        'meeting_schedule' => 'date',
        'form_data' => 'array',
        'processed_by_java_server' => 'boolean',
        'processing_date' => 'datetime',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function retailerListings()
    {
        return $this->hasMany(RetailerListing::class);
    }

    public function getStatus()
    {
        return $this->status;
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

    public function isProcessedByJavaServer()
    {
        return $this->processed_by_java_server;
    }

    public function markAsProcessed()
    {
        $this->processed_by_java_server = true;
        $this->processing_date = now();
        return $this->save();
    }

    public function getPdfUrl()
    {
        return $this->pdf_path ? asset($this->pdf_path) : null;
    }

    public function getFormDataAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function getDaysToMeeting()
    {
        if (!$this->meeting_schedule) {
            return null;
        }
        return now()->diffInDays($this->meeting_schedule, false);
    }

    public function getStatusColor()
    {
        return match ($this->status) {
            'approved' => 'green',
            'partially approved' => 'yellow',
            'pending' => 'gray',
            'rejected' => 'red',
            default=> 'gray',

        };
    }
}
