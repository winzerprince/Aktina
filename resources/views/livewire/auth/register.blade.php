<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $role = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'role' => ['required', 'string', 'in:admin,production_manager,hr_manager,vendor,retailer,supplier'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ],
        [
        'name.required' => 'Please enter your name.',
        'email.required' => 'Please enter your email address.',
        'email.email' => 'Please enter a valid email address with the form example@gmail.com.',
        'email.unique' => 'This email is already registered.',
        'role.required' => 'Please select a role.',
        'role.in' => 'Please select a valid role.',
        'password.required' => 'Please enter a password.',
        'password.confirmed' => 'Passwords do not match.'
    ]
        );

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        event(new Registered(($user )));

        Auth::login($user);

        $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
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
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
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
            box-shadow: 0 15px 35px -5px rgba(37, 99, 235, 0.4);
        }
        
        .form-group {
            animation: fadeInUp 0.6s ease-out;
        }
        
        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }
        .form-group:nth-child(4) { animation-delay: 0.4s; }
        .form-group:nth-child(5) { animation-delay: 0.5s; }
        .form-group:nth-child(6) { animation-delay: 0.6s; }
        
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
        
        .select-focus {
            transition: all 0.3s ease;
        }
        
        .select-focus:focus {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.2);
        }
        
        .role-icon {
            display: inline-block;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            margin-right: 8px;
        }
        
        .role-admin { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .role-production { background: linear-gradient(135deg, #10b981, #059669); }
        .role-hr { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
        .role-vendor { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .role-retailer { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .role-supplier { background: linear-gradient(135deg, #06b6d4, #0891b2); }
    </style>

    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-orange-600 bg-clip-text text-transparent mb-2">
            {{ __('Create Your Account') }}
        </h2>
        <p class="text-gray-600 dark:text-gray-300">
            {{ __('Join us and start managing your supply chain efficiently') }}
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="text-center mb-4" :status="session('status')" />

    <form wire:submit="register" class="space-y-6" novalidate>
        <!-- Name -->
        <div class="form-group">
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ __('Full Name') }}
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <input
                    wire:model="name"
                    type="text"
                    id="name"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="{{ __('Enter your full name') }}"
                    class="input-focus block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                />
            </div>
            @error('name') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

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
                    autocomplete="email"
                    placeholder="email@example.com"
                    class="input-focus block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                />
            </div>
            @error('email') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- Role Selection -->
        <div class="form-group">
            <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ __('Select Your Role') }}
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V6z"/>
                    </svg>
                </div>
                <select
                    wire:model="role"
                    id="role"
                    required
                    class="select-focus block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                >
                    <option value="" disabled selected>{{ __('Choose your role') }}</option>
                    <option value="admin">
                        {{ __('Admin') }}
                    </option>
                    <option value="production_manager">
                        {{ __('Production Manager') }}
                    </option>
                    <option value="hr_manager">
                        {{ __('HR Manager') }}
                    </option>
                    <option value="vendor">
                        {{ __('Vendor') }}
                    </option>
                    <option value="retailer">
                        {{ __('Retailer') }}
                    </option>
                    <option value="supplier">
                        {{ __('Supplier') }}
                    </option>
                </select>
            </div>
            @error('role') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ __('Password') }}
            </label>
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
                    autocomplete="new-password"
                    placeholder="{{ __('Create a strong password') }}"
                    class="input-focus block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                />
            </div>
            @error('password') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ __('Confirm Password') }}
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <input
                    wire:model="password_confirmation"
                    type="password"
                    id="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="{{ __('Confirm your password') }}"
                    class="input-focus block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                />
            </div>
            @error('password_confirmation') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- Submit Button -->
        <div class="form-group">
            <button
                type="submit"
                class="btn-gradient w-full py-3 px-4 rounded-lg text-white font-medium text-sm relative overflow-hidden transform transition-all duration-300 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
                {{ __('Create Account') }}
            </button>
        </div>
    </form>

    <div class="mt-8 text-center">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ __('Already have an account?') }}
            <a href="{{ route('login') }}" 
               class="font-medium text-emerald-600 hover:text-emerald-500 transition-colors ml-1"
               wire:navigate>
                {{ __('Sign in here') }}
            </a>
        </p>
    </div>
</div>
