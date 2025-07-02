<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'verified' => 'boolean',
            'address' => 'array',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    // Communication relationships
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_user')
                    ->withTimestamps();
    }

    public function conversationsAsUser1()
    {
        return $this->hasMany(Conversation::class, 'user1_id');
    }

    public function conversationsAsUser2()
    {
        return $this->hasMany(Conversation::class, 'user2_id');
    }

    // Inventory relationships
    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class, 'moved_by');
    }

    public function resolvedAlerts()
    {
        return $this->hasMany(InventoryAlert::class, 'resolved_by');
    }

    // Analytics relationships
    public function dailyMetrics()
    {
        return $this->hasMany(DailyMetric::class);
    }

    public function salesAnalytics()
    {
        return $this->hasMany(SalesAnalytic::class);
    }

    // Role-based relationships
    public function supplier()
    {
        return $this->hasOne(Supplier::class);
    }

    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

    public function hrManager()
    {
        return $this->hasOne(HrManager::class);
    }

    public function productionManager()
    {
        return $this->hasOne(ProductionManager::class);
    }

    public function vendor()
    {
        return $this->hasOne(Vendor::class);
    }

    public function retailer()
    {
        return $this->hasOne(Retailer::class);
    }

    // Order relationships
    public function buyerOrders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    public function sellerOrders()
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    // Role checking methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isSupplier()
    {
        return $this->role === 'supplier';
    }

    public function isVendor()
    {
        return $this->role === 'vendor';
    }

    public function isRetailer()
    {
        return $this->role === 'retailer';
    }

    public function isHrManager()
    {
        return $this->role === 'hr_manager';
    }

    public function isProductionManager()
    {
        return $this->role === 'production_manager';
    }

    // Additional role checking methods for tech industry
    public function canManageComponents()
    {
        return in_array($this->role, ['admin', 'production_manager', 'supplier']);
    }

    public function canViewProduction()
    {
        return in_array($this->role, ['admin', 'production_manager']);
    }

    public function canManageSuppliers()
    {
        return in_array($this->role, ['admin', 'hr_manager']);
    }

    public function getCompanyTypeAttribute()
    {
        if ($this->isSupplier()) {
            return 'Component Supplier';
        } elseif ($this->isVendor()) {
            return 'Distribution Partner';
        } elseif ($this->isRetailer()) {
            return 'Retail Partner';
        }
        return 'Aktina Technologies';
    }
}
