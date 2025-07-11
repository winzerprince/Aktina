<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $table = 'vendor';

    protected $fillable = [
        'user_id',
        'retailer_listing_id',
        'application_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function retailerListing()
    {
        return $this->belongsTo(RetailerListing::class);
    }

    public function retailers()
    {
        return $this->hasMany(Retailer::class);
    }
}
