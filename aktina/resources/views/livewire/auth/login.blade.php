<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Fill credentials for testing
     */
    public function fillCredentials($email): void
    {
        $this->email = $email;
        $this->password = 'password';

        // Get role name for the message
        $role = '';
        if (str_contains($email, 'supplier')) $role = 'Supplier';
        elseif (str_contains($email, 'production')) $role = 'Production Manager';
        elseif (str_contains($email, 'hr.manager')) $role = 'HR Manager';
        elseif (str_contains($email, 'admin')) $role = 'System Administrator';
        elseif (str_contains($email, 'wholesaler')) $role = 'Wholesaler';
        elseif (str_contains($email, 'retailer')) $role = 'Retailer';

        session()->flash('credential_filled', "Credentials filled for {$role}. Click 'Log in' to continue.");
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

    <!-- Test Credentials Panel -->
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
        <h3 class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-3">🧪 Test Credentials</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
            <div class="space-y-2">
                <button wire:click="fillCredentials('john.supplier@aktina.test')" type="button" class="w-full p-2 bg-white dark:bg-blue-800/30 rounded border hover:bg-blue-50 dark:hover:bg-blue-800/50 transition-colors text-left">
                    <div class="font-medium text-blue-900 dark:text-blue-100">Supplier</div>
                    <div class="text-blue-700 dark:text-blue-200">john.supplier@aktina.test</div>
                    <div class="text-blue-600 dark:text-blue-300">Password: password</div>
                </button>
                <button wire:click="fillCredentials('jane.production@aktina.test')" type="button" class="w-full p-2 bg-white dark:bg-blue-800/30 rounded border hover:bg-blue-50 dark:hover:bg-blue-800/50 transition-colors text-left">
                    <div class="font-medium text-blue-900 dark:text-blue-100">Production Manager</div>
                    <div class="text-blue-700 dark:text-blue-200">jane.production@aktina.test</div>
                    <div class="text-blue-600 dark:text-blue-300">Password: password</div>
                </button>
                <button wire:click="fillCredentials('mike.hr.manager@aktina.test')" type="button" class="w-full p-2 bg-white dark:bg-blue-800/30 rounded border hover:bg-blue-50 dark:hover:bg-blue-800/50 transition-colors text-left">
                    <div class="font-medium text-blue-900 dark:text-blue-100">HR Manager</div>
                    <div class="text-blue-700 dark:text-blue-200">mike.hr.manager@aktina.test</div>
                    <div class="text-blue-600 dark:text-blue-300">Password: password</div>
                </button>
            </div>
            <div class="space-y-2">
                <button wire:click="fillCredentials('sarah.admin@aktina.test')" type="button" class="w-full p-2 bg-white dark:bg-blue-800/30 rounded border hover:bg-blue-50 dark:hover:bg-blue-800/50 transition-colors text-left">
                    <div class="font-medium text-blue-900 dark:text-blue-100">System Administrator</div>
                    <div class="text-blue-700 dark:text-blue-200">sarah.admin@aktina.test</div>
                    <div class="text-blue-600 dark:text-blue-300">Password: password</div>
                </button>
                <button wire:click="fillCredentials('bob.wholesaler@aktina.test')" type="button" class="w-full p-2 bg-white dark:bg-blue-800/30 rounded border hover:bg-blue-50 dark:hover:bg-blue-800/50 transition-colors text-left">
                    <div class="font-medium text-blue-900 dark:text-blue-100">Wholesaler</div>
                    <div class="text-blue-700 dark:text-blue-200">bob.wholesaler@aktina.test</div>
                    <div class="text-blue-600 dark:text-blue-300">Password: password</div>
                </button>
                <button wire:click="fillCredentials('alice.retailer@aktina.test')" type="button" class="w-full p-2 bg-white dark:bg-blue-800/30 rounded border hover:bg-blue-50 dark:hover:bg-blue-800/50 transition-colors text-left">
                    <div class="font-medium text-blue-900 dark:text-blue-100">Retailer</div>
                    <div class="text-blue-700 dark:text-blue-200">alice.retailer@aktina.test</div>
                    <div class="text-blue-600 dark:text-blue-300">Password: password</div>
                </button>
            </div>
        </div>
        <p class="text-xs text-blue-600 dark:text-blue-400 mt-2">
            💡 Click any credential card above to auto-fill the login form, then click "Log in"
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <!-- Credential filled message -->
    @if (session('credential_filled'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3 text-center">
            <p class="text-sm text-green-800 dark:text-green-200">{{ session('credential_filled') }}</p>
        </div>
    @endif

    <form wire:submit="login" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Email address')"
            type="email"
            required
            autofocus
            autocomplete="email"
            placeholder="email@example.com"
        />

        <!-- Password -->
        <div class="relative">
            <flux:input
                wire:model="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="current-password"
                :placeholder="__('Password')"
                viewable
            />

            @if (Route::has('password.request'))
                <flux:link class="absolute end-0 top-0 text-sm" :href="route('password.request')" wire:navigate>
                    {{ __('Forgot your password?') }}
                </flux:link>
            @endif
        </div>

        <!-- Remember Me -->
        <flux:checkbox wire:model="remember" :label="__('Remember me')" />

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full">{{ __('Log in') }}</flux:button>
        </div>
    </form>

    @if (Route::has('register'))
        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            {{ __('Don\'t have an account?') }}
            <flux:link :href="route('register')" wire:navigate>{{ __('Sign up') }}</flux:link>
        </div>
    @endif
</div>
