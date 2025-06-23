<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetailerListing extends Model
{
    use HasFactory;

    protected $table = 'retailer_listing';

    protected $fillable = [
        'retailer_email',
        'application_id',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function vendors()
    {
        return $this->hasMany(Vendor::class);
    }
}
