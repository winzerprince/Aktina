<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $table = 'applications';

    protected $guarded = [];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_SCORED = 'scored';
    const STATUS_MEETING_SCHEDULED = 'meeting_scheduled';
    const STATUS_MEETING_COMPLETED = 'meeting_completed';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $casts = [
        'meeting_schedule' => 'date',
        'form_data' => 'array',
        'processed_by_java_server' => 'boolean',
        'processing_date' => 'datetime',
        'score' => 'integer',
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

    public function isScored()
    {
        return $this->status === self::STATUS_SCORED;
    }

    public function isMeetingScheduled()
    {
        return $this->status === self::STATUS_MEETING_SCHEDULED;
    }

    public function isMeetingCompleted()
    {
        return $this->status === self::STATUS_MEETING_COMPLETED;
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
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
            self::STATUS_APPROVED => 'green',
            self::STATUS_SCORED => 'blue',
            self::STATUS_MEETING_SCHEDULED => 'yellow',
            self::STATUS_MEETING_COMPLETED => 'purple',
            self::STATUS_PENDING => 'gray',
            self::STATUS_REJECTED => 'red',
            default => 'gray',
        };
    }

    public function markAsScored(int $score)
    {
        $this->score = $score;
        $this->status = self::STATUS_SCORED;
        $this->processed_by_java_server = true;
        $this->processing_date = now();
        return $this->save();
    }

    public function scheduleMeeting($date)
    {
        $this->meeting_schedule = $date;
        $this->status = self::STATUS_MEETING_SCHEDULED;
        return $this->save();
    }

    public function completeMeeting($notes = null)
    {
        $this->meeting_notes = $notes;
        $this->status = self::STATUS_MEETING_COMPLETED;
        return $this->save();
    }

    /**
     * Get the current progress step for the progress indicator component
     */
    public function getProgressStep(): int
    {
        return match ($this->status) {
            self::STATUS_PENDING => 1,
            self::STATUS_SCORED => 2,
            self::STATUS_MEETING_SCHEDULED, self::STATUS_MEETING_COMPLETED => 3,
            self::STATUS_APPROVED, self::STATUS_REJECTED => 4,
            default => 1,
        };
    }
}
