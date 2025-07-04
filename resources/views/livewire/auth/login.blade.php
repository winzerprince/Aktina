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

        //Role-based redirection
        $role = auth()->user()->role ?? null;
        if ($role === 'Retailer') {
            $this->redirectIntended(default: route('retailer.dashboard', absolute: false), navigate: true);
        }
        elseif ($role === 'Admin') {
            $this->redirectIntended(default: route('admin.dashboard', absolute: false), navigate: true);
        }
        elseif ($role === 'Production Manager') {
            $this->redirectIntended(default: route('production_manager.dashboard', absolute: false), navigate: true);
        }
        elseif ($role === 'HR Manager') {
            $this->redirectIntended(default: route('hr_manager.dashboard', absolute: false), navigate: true);
        }
        elseif ($role === 'Supplier') {
           $this->redirectIntended(default: route('supplier.dashboard', absolute: false), navigate: true);
        }
        elseif ($role === 'Vendor') {
           $this->redirectIntended(default: route('vendor.dashboard', absolute: false), navigate: true);
        }
        else {
            $this->redirectIntended(default: route('access.denied', absolute: false), navigate: true);
        }
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

<div
    class="w-full max-w-md border rounded-lg p-6 my-16 text-zinc-900 dark:text-zinc-100"
    style="border-color: #008800; box-shadow: 0 4px 24px 0 #00880033;"
    id="login-border"
>
    <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="login" class="flex flex-col gap-6" novalidate>
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
            <flux:button
                type="submit"
                class="w-full border-none"
                style="background-color: #30cf36; color: #fff; border: none; transition: background 0.2s;"
                onmouseover="this.style.backgroundColor = (document.documentElement.classList.contains('dark') ? '#30cf36' : '#008800')"
                onmouseout="this.style.backgroundColor = (document.documentElement.classList.contains('dark') ? '#044c03' : '#30cf36')"
            >
                {{ __('Log in') }}
            </flux:button>
        </div>
    </form>

    @if (Route::has('register'))
        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            {{ __('Don\'t have an account?') }}
            <flux:link :href="route('register')" wire:navigate>{{ __('Sign up') }}</flux:link>
        </div>
    @endif
</div>
