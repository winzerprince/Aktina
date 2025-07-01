<?php

namespace App\Providers;

use App\Models\Application;
use App\Policies\ApplicationPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Application::class => ApplicationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define additional gates if needed
        Gate::define('admin-access', function ($user) {
            return $user->hasRole('admin');
        });

        Gate::define('vendor-verification', function ($user) {
            return $user->hasRole('vendor') && !$user->is_verified;
        });

        Gate::define('retailer-verification', function ($user) {
            return $user->hasRole('retailer') && !$user->is_verified;
        });
    }
}
