<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <!-- Include the head partial that contains meta tags, CSS, and JavaScript references -->
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <!-- Main sidebar component: sticky (stays in place when scrolling) and stashable (can be hidden) -->
        <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <!-- Toggle button for the sidebar, visible only on mobile devices (hidden on lg screens) -->
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <!-- Application logo with link to dashboard, using wire:navigate for SPA-like navigation -->
            <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group :heading="__()" class="grid">
                <flux:navlist.group :heading="__('Platform')" class="grid">
                </flux:navlist.group>
            </flux:navlist>


            <!-- Get the current user's role for role-based navigation -->
            @php
                $role = auth()->user()->role ?? null;
            @endphp

            @if ($role === 'Retailer')
            <!-- Retailer-specific navigation items -->
                <flux:navlist variant="outline">
                    <flux:navlist.group class="grid">
                        <flux:navlist.item :href="route('retailer.feedback')" :current="request()->routeIs('retailer.feedback')" wire:navigate>
                            {{ __('Customer Feedback') }}
                        </flux:navlist.item>
                        <flux:navlist.item :href="route('retailer.sales_insights')" :current="request()->routeIs('retailer.sales_insights')" wire:navigate>
                            {{ __('Sales Insights') }}
                        </flux:navlist.item>
                        <flux:navlist.item :href="route('retailer.order_placement')" :current="request()->routeIs('retailer.order_placement')" wire:navigate>
                            {{ __('Order Placement') }}
                        </flux:navlist.item>
                    </flux:navlist.group>
                </flux:navlist>
            @endif

            @if ($role === 'Admin')
            <!-- Admin-specific navigation items for system management -->
                <flux:navlist variant="outline">
                    <flux:navlist.group class="grid">
                        <flux:navlist.item :href="route('admin.financial_analysis')" :current="request()->routeIs('admin.financial_analysis')" wire:navigate>
                            {{ __('Financial Analysis') }}
                        </flux:navlist.item>
                        <flux:navlist.item :href="route('admin.strategic_insight')" :current="request()->routeIs('admin.strategic_insight')" wire:navigate>
                            {{ __('Strategic Insight') }}
                        </flux:navlist.item>
                        <flux:navlist.item :href="route('admin.user_access')" :current="request()->routeIs('admin.user_access')" wire:navigate>
                            {{ __('User Access') }}
                        </flux:navlist.item>
                    </flux:navlist.group>
                </flux:navlist>
            @endif

             @if ($role === 'Vendor')
             <!-- Vendor-specific navigation items for product providers -->
                <flux:navlist variant="outline">
                    <flux:navlist.group class="grid">
                        <flux:navlist.item :href="route('vendor.order_management')" :current="request()->routeIs('vendor.order_management')" wire:navigate>
                            {{ __('Order Management') }}
                        </flux:navlist.item>
                        <flux:navlist.item :href="route('vendor.AI_assistant')" :current="request()->routeIs('vendor.ai_assistant')" wire:navigate>
                            {{ __('AI Assistant') }}
                        </flux:navlist.item>
                    </flux:navlist.group>
                </flux:navlist>
            @endif

             @if ($role === 'Supplier')
             <!-- Supplier-specific navigation items for inventory management -->
                <flux:navlist variant="outline">
                    <flux:navlist.group class="grid">
                        <flux:navlist.item :href="route('supplier.order_statistics')" :current="request()->routeIs('supplier.order_statistics')" wire:navigate>
                            {{ __('Orders') }}
                        </flux:navlist.item>
                        <flux:navlist.item :href="route('supplier.delivery_metrics')" :current="request()->routeIs('supplier.delivery_metrics')" wire:navigate>
                            {{ __('Delivery Metrics') }}
                        </flux:navlist.item>
                    </flux:navlist.group>
                </flux:navlist>
            @endif

             @if ($role === 'HR Manager')
             <!-- HR Manager-specific navigation items for personnel management -->
                <flux:navlist variant="outline">
                    <flux:navlist.group class="grid">
                        <flux:navlist.item :href="route('hr_manager.workforce_analytics')" :current="request()->routeIs('hr_manager.workforce_analytics')" wire:navigate>
                            {{ __('Workforce Analytics') }}
                        </flux:navlist.item>
                        <flux:navlist.item :href="route('hr_manager.AI_assistant')" :current="request()->routeIs('hr_manager.ai_assistant')" wire:navigate>
                            {{ __('AI Assistant') }}
                        </flux:navlist.item>
                        <flux:navlist.item :href="route('hr_manager.staff_performance')" :current="request()->routeIs('hr_manager.staff_performance')" wire:navigate>
                            {{ __('Staff Performance') }}
                        </flux:navlist.item>
                    </flux:navlist.group>
                </flux:navlist>
            @endif

                <!-- Production Manager-specific navigation items for manufacturing oversight -->
            @if ($role === 'Production Manager')
                <flux:navlist variant="outline">
                    <flux:navlist.group class="grid">
                        <flux:navlist.item :href="route('production_manager.order_management')" :current="request()->routeIs('production_manager.order_management')" wire:navigate>
                            {{ __('Order Management') }}
                        </flux:navlist.item>
                        <flux:navlist.item :href="route('production_manager.inventory_alerts')" :current="request()->routeIs('production_manager.inventory_alerts')" wire:navigate>
                            {{ __('Inventory Alerts') }}
                        </flux:navlist.item>
                        <flux:navlist.item :href="route('production_manager.production_metrics')" :current="request()->routeIs('production_manager.production_metrics')" wire:navigate>
                            {{ __('Production Metrics') }}
                        </flux:navlist.item>
                    </flux:navlist.group>
                </flux:navlist>
            @endif
             <flux:navlist variant="outline">

             <!-- Communication link available to all user roles -->
                <flux:navlist.group class="grid">
                    <flux:navlist.item :href="route('communication')" :current="request()->routeIs('communication')" wire:navigate>{{ __('Communication') }}</flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />
            <!-- Spacer to push the bottom content to the end of the sidebar -->

            <flux:navlist variant="outline">
            <!-- External links section - currently commented out -->
                <!--<flux:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                {{ __('Repository') }}
                </flux:navlist.item>

                <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                {{ __('Documentation') }}
                </flux:navlist.item>-->
            </flux:navlist>

            <!-- Desktop User Menu -->
            <!-- Desktop User Menu - only visible on large screens -->
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon:trailing="chevrons-up-down"
                /></flux:profile>

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                        <!-- User profile summary with avatar, name and email -->
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
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                    <!-- User settings link -->
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                    <!-- Logout form -->
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <!-- Mobile sidebar toggle button -->
            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                        <!-- Mobile user profile summary with avatar, name and email -->
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
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    <!-- Mobile user settings link -->
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                    <!-- Mobile logout form -->
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        <!-- Content slot where the main page content will be rendered -->
        @fluxScripts
    </body>
        <!-- Include Flux JavaScript utilities and components -->
</html>
