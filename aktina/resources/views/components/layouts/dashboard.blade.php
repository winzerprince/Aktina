<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:header sticky class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <div class="flex items-center justify-between w-full px-6">
            <!-- Logo -->
            <div class="flex items-center space-x-3">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2" wire:navigate>
                    <x-app-logo />
                </a>
                <div class="hidden md:flex flex-col">
                    <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Aktina Supply Chain</span>
                    <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ auth()->user()->getRoleDisplayName() }}</span>
                </div>
            </div>

            <!-- Notifications and Profile -->
            <div class="flex items-center space-x-4">
                <!-- Notifications -->
                <flux:button icon="bell" variant="ghost" class="relative">
                    <flux:badge color="red" size="sm" class="absolute -top-1 -right-1 h-4 w-4 text-xs">3</flux:badge>
                </flux:button>

                <!-- Profile Dropdown -->
                <flux:dropdown position="bottom" align="end">
                    <flux:profile
                        :name="auth()->user()->name"
                        :initials="auth()->user()->initials()"
                        icon:trailing="chevron-down"
                    />

                    <flux:menu class="w-[220px]">
                        <flux:menu.radio.group>
                            <div class="p-0 text-sm font-normal">
                                <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                    <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                        <span
                                            class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                        >
                                            {{ auth()->user()->initials() }}
                                        </span>
                                    </span>

                                    <div class="grid flex-1 text-start text-sm leading-tight">
                                        <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                        <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                        <span class="truncate text-xs text-zinc-500">{{ auth()->user()->getRoleDisplayName() }}</span>
                                    </div>
                                </div>
                            </div>
                        </flux:menu.radio.group>

                        <flux:menu.separator />

                        <flux:menu.radio.group>
                            <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                        </flux:menu.radio.group>

                        <flux:menu.separator />

                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                                {{ __('Log Out') }}
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            </div>
        </div>
    </flux:header>

    <flux:main class="px-6 py-6">
        {{ $slot }}
    </flux:main>

    @fluxScripts
</body>
</html>
