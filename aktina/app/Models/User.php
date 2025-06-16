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
     * The available user roles
     */
    public const ROLES = [
        'supplier' => 'Supplier',
        'production_manager' => 'Production Manager',
        'hr_manager' => 'HR Manager',
        'system_administrator' => 'System Administrator',
        'wholesaler' => 'Wholesaler',
        'retailer' => 'Retailer',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Get the user's role display name
     */
    public function getRoleDisplayName(): string
    {
        return self::ROLES[$this->role] ?? 'Unknown';
    }

    /**
     * Check if user is a supplier
     */
    public function isSupplier(): bool
    {
        return $this->hasRole('supplier');
    }

    /**
     * Check if user is a production manager
     */
    public function isProductionManager(): bool
    {
        return $this->hasRole('production_manager');
    }

    /**
     * Check if user is an HR manager
     */
    public function isHRManager(): bool
    {
        return $this->hasRole('hr_manager');
    }

    /**
     * Check if user is a system administrator
     */
    public function isSystemAdministrator(): bool
    {
        return $this->hasRole('system_administrator');
    }

    /**
     * Check if user is a wholesaler
     */
    public function isWholesaler(): bool
    {
        return $this->hasRole('wholesaler');
    }

    /**
     * Check if user is a retailer
     */
    public function isRetailer(): bool
    {
        return $this->hasRole('retailer');
    }

    /**
     * Get the dashboard route based on user role
     */
    public function getDashboardRoute(): string
    {
        return match ($this->role) {
            'supplier' => 'dashboard.supplier',
            'production_manager' => 'dashboard.production-manager',
            'hr_manager' => 'dashboard.hr-manager',
            'system_administrator' => 'dashboard.system-administrator',
            'wholesaler' => 'dashboard.wholesaler',
            'retailer' => 'dashboard.retailer',
            default => 'dashboard',
        };
    }

    /**
     * Get orders where this user is the supplier
     */
    public function supplierOrders()
    {
        return $this->hasMany(Order::class, 'supplier_id');
    }

    /**
     * Get orders where this user is the production manager
     */
    public function managedOrders()
    {
        return $this->hasMany(Order::class, 'production_manager_id');
    }
}
