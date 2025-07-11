<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $table = 'vendors';

    protected $guarded = [
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function application()
    {
        return $this->hasOne(Application::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function connectedRetailers()
    {
        return $this->hasManyThrough(
            User::class,
            Application::class,
            'vendor_id', // Foreign key on applications table
            'id', // Foreign key on users table
            'id', // Local key on vendors table
            'id' // Local key on applications table
        )->join('retailer_listings', 'applications.id', '=', 'retailer_listings.application_id')
         ->join('users as retailers', 'retailer_listings.retailer_id', '=', 'retailers.id')
         ->where('retailers.role', 'retailer')
         ->select('retailers.*')
         ->distinct();
    }

    public function retailerListings()
    {
        return $this->hasManyThrough(
            RetailerListing::class,
            Application::class,
            'vendor_id', // Foreign key on applications table
            'application_id', // Foreign key on retailer_listings table
            'id', // Local key on vendors table
            'id' // Local key on applications table
        );
    }
}
