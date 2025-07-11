<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(600)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('file-upload', function (Request $request) {
            return Limit::perHour(1000)->by($request->user()?->id ?: $request->ip())
                ->response(function () {
                    return response('Too many file upload attempts. Please try again later.', 429);
                });
        });

        RateLimiter::for('verification-form', function (Request $request) {
            return Limit::perMinute(100)->by($request->user()?->id ?: $request->ip())
                ->response(function () {
                    return response('Too many form submissions. Please slow down.', 429);
                });
        });

        RateLimiter::for('admin-actions', function (Request $request) {
            return Limit::perMinute(300)->by($request->user()?->id ?: $request->ip())
                ->response(function () {
                    return response('Too many admin actions. Please slow down.', 429);
                });
        });
    }
}
