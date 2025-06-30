<?php

namespace App\Http\Middleware;

use App\Interfaces\Services\VerificationServiceInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureRoleVerified
{
    public function __construct(
        private VerificationServiceInterface $verificationService
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Admin users bypass all verification requirements
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Check if user is fully verified
        if (!$this->verificationService->isUserFullyVerified($user)) {
            return $this->redirectToVerificationView($user);
        }

        return $next($request);
    }

    /**
     * Redirect user to appropriate verification view based on role
     */
    private function redirectToVerificationView($user)
    {
        return match ($user->role) {
            'vendor' => redirect()->route('verification.vendor'),
            'retailer' => redirect()->route('verification.retailer'),
            'supplier' => redirect()->route('verification.supplier'),
            'production_manager' => redirect()->route('verification.production-manager'),
            'hr_manager' => redirect()->route('verification.hr-manager'),
            default => redirect()->route('verification.general'),
        };
    }
}
