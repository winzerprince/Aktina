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
        if ($role === 'retailer') {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
        }
        elseif ($role === 'admin') {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
        }
        elseif ($role === 'production_manager') {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
        }
        elseif ($role === 'hr_manager') {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
        }
        elseif ($role === 'supplier') {
           $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
        }
        elseif ($role === 'vendor') {
           $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
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
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 100)) {
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

<div class="w-full">
    <style>
        .input-focus {
            transition: all 0.3s ease;
        }

        .input-focus:focus {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.2);
        }

        .btn-gradient {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-gradient:hover::before {
            left: 100%;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px -5px rgba(16, 185, 129, 0.4);
        }

        .form-group {
            animation: fadeInUp 0.6s ease-out;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }
        .form-group:nth-child(4) { animation-delay: 0.4s; }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .checkbox-custom {
            position: relative;
            cursor: pointer;
        }

        .checkbox-custom input[type="checkbox"] {
            opacity: 0;
            position: absolute;
        }

        .checkbox-custom .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 20px;
            width: 20px;
            background: linear-gradient(135deg, #e5e7eb 0%, #f3f4f6 100%);
            border-radius: 4px;
            border: 2px solid #d1d5db;
            transition: all 0.3s ease;
        }

        .checkbox-custom input[type="checkbox"]:checked ~ .checkmark {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-color: #10b981;
        }

        .checkbox-custom .checkmark:after {
            content: "";
            position: absolute;
            display: none;
        }

        .checkbox-custom input[type="checkbox"]:checked ~ .checkmark:after {
            display: block;
        }

        .checkbox-custom .checkmark:after {
            left: 7px;
            top: 3px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }
    </style>

    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-blue-600 bg-clip-text text-transparent mb-2">
            {{ __('Welcome Back!') }}
        </h2>
        <p class="text-gray-600 dark:text-gray-300">
            {{ __('Enter your credentials to access your account') }}
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="text-center mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-6" novalidate>
        <!-- Email Address -->
        <div class="form-group">
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ __('Email Address') }}
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                    </svg>
                </div>
                <input
                    wire:model="email"
                    type="email"
                    id="email"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="email@example.com"
                    class="input-focus block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                />
            </div>
            @error('email') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
            <div class="flex justify-between items-center mb-2">
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Password') }}
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-sm text-orange-600 hover:text-orange-500 transition-colors"
                       wire:navigate>
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <input
                    wire:model="password"
                    type="password"
                    id="password"
                    required
                    autocomplete="current-password"
                    placeholder="{{ __('Enter your password') }}"
                    class="input-focus block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                />
            </div>
            @error('password') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- Remember Me -->
        <div class="form-group flex items-center">
            <label class="checkbox-custom flex items-center cursor-pointer">
                <input type="checkbox" wire:model="remember" class="sr-only">
                <span class="checkmark"></span>
                <span class="ml-8 text-sm text-gray-700 dark:text-gray-300">{{ __('Remember me') }}</span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="form-group">
            <button
                type="submit"
                class="btn-gradient w-full py-3 px-4 rounded-lg text-white font-medium text-sm relative overflow-hidden transform transition-all duration-300 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
            >
                {{ __('Sign In') }}
            </button>
        </div>
    </form>

    @if (Route::has('register'))
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ __("Don't have an account?") }}
                <a href="{{ route('register') }}"
                   class="font-medium text-blue-600 hover:text-blue-500 transition-colors ml-1"
                   wire:navigate>
                    {{ __('Create one now') }}
                </a>
            </p>
        </div>
    @endif
</div>
