<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <!--this first flux handles the whole side bar-->
        <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            @php
                $role = auth()->user()->role ?? '';
            @endphp
            <div class="text-lg font-extrabold tracking-tight text-green-800 dark:text-zinc-200" >
                {{ __($role . ' Dashboard') }}
            </div>

            <flux:navlist variant="outline">
                <flux:navlist.group class="grid " >
                    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Home') }}</flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>




            @if ($role === 'Retailer')
                <flux:navlist variant="outline">
                    <flux:navlist.group class="grid">
                        <flux:navlist.item :href="route('retailer.overview')" :current="request()->routeIs('retailer.overview')" wire:navigate>
                            {{ __('Overview')}}
                        </flux:navlist.item>
                        <flux:navlist.item :href="route('retailer.orders')" :current="request()->routeIs('retailer.orders')" wire:navigate>
                            {{ __('Orders')}}
                        </flux:navlist.item>
                        <flux:navlist.item :href="route('retailer.sales')" :current="request()->routeIs('retailer.sales')" wire:navigate>
                            {{ __('Sales')}}
                        </flux:navlist.item>
                        <flux:navlist.item :href="route('retailer.inventory')" :current="request()->routeIs('retailer.inventory')" wire:navigate>
                            {{ __('Inventory')}}
                        </flux:navlist.item>
                        <flux:navlist.item :href="route('retailer.ratings')" :current="request()->routeIs('retailer.ratings')" wire:navigate>
                            {{ __('Ratings')}}
                        </flux:navlist.item>
                    </flux:navlist.group>
                </flux:navlist>
            @endif
           <!-- Admin role-->
            @if ($role === 'Admin')
                <flux:navlist variant="outline">
                    <flux:navlist.group :heading="__('Administration')" class="grid" >
                        <flux:navlist.item icon="home" :href="route('admin.overview')" :current="request()->routeIs('admin.overview')" wire:navigate>
                            {{ __('Overview')}}
                        </flux:navlist.item>
                        <flux:navlist.item icon="shopping-bag" :href="route('admin.sales')" :current="request()->routeIs('admin.sales')" wire:navigate>
                            {{ __('Sales')}}
                        </flux:navlist.item>
                        <flux:navlist.group
                            :heading="__('User Management')"
                            class="grid"
                            expandable
                            :expanded="request()->routeIs('admin.users', 'admin.vendors', 'admin.pending-signups')">
                            <flux:navlist.item icon="users" :href="route('admin.users')" :current="request()->routeIs('admin.users')" wire:navigate>
                                {{__('Users')}}
                            </flux:navlist.item>
                            <flux:navlist.item icon="building-storefront" :href="route('admin.vendors')" :current="request()->routeIs('admin.vendors')" wire:navigate>
                                {{__('Vendors')}}
                            </flux:navlist.item>
                            <flux:navlist.item icon="user-plus" :href="route('admin.pending-signups')" :current="request()->routeIs('admin.pending-signups')" wire:navigate>
                                {{__('Pending Signups')}}
                            </flux:navlist.item>
                        </flux:navlist.group>

                        <flux:navlist.group
                            :heading="__('Insights')"
                            class="grid"
                            expandable
                            :expanded="request()->routeIs('admin.trends-and-predictions', 'admin.important-metrics', 'admin.customer-insights')">
                            <flux:navlist.item icon="chart-bar" :href="route('admin.trends-and-predictions')" :current="request()->routeIs('admin.trends-and-predictions')" wire:navigate>
                                {{__('Trends and Predictions')}}
                            </flux:navlist.item>
                            <flux:navlist.item icon="presentation-chart-line" :href="route('admin.important-metrics')" :current="request()->routeIs('admin.important-metrics')" wire:navigate>
                                {{__('Important Metrics')}}
                            </flux:navlist.item>
                            <flux:navlist.item icon="user-group" :href="route('admin.customer-insights')" :current="request()->routeIs('admin.customer-insights')" wire:navigate>
                                {{__('Customer Insights')}}
                            </flux:navlist.item>
                        </flux:navlist.group>
                    </flux:navlist.group>
                </flux:navlist>
            @endif



            <!-- Role-based Navigation -->
             @if ($role === 'Vendor')
                <flux:navlist variant="outline">
                    <flux:navlist.group class="grid">
                        <flux:navlist.item :href="route('vendor.overview')" :current="request()->routeIs('vendor.overview')" wire:navigate>
                            {{ __('Overview') }}
                        </flux:navlist.item>
                        <flux:navlist.item :href="route('vendor.orders')" :current="request()->routeIs('vendor.orders')" wire:navigate>
                            {{ __('Orders') }}
                        </flux:navlist.item>
                        <flux:navlist.item :href="route('vendor.sales')" :current="request()->routeIs('vendor.sales')" wire:navigate>
                            {{ __('Sales') }}
                        </flux:navlist.item>
                        <flux:navlist.item :href="route('vendor.inventory')" :current="request()->routeIs('vendor.inventory')" wire:navigate>
                            {{ __('Inventory') }}
                        </flux:navlist.item>
                        <flux:navlist.item :href="route('vendor.retailers')" :current="request()->routeIs('vendor.retailers')" wire:navigate>
                            {{ __('Retailers') }}
                        </flux:navlist.item>
                    </flux:navlist.group>
                </flux:navlist>
            @endif

             @if ($role === 'Supplier')
                <flux:navlist variant="outline">
                    <flux:navlist.group class="grid">
                        <flux:navlist.item :href="route('supplier.overview')" :current="request()->routeIs('supplier.overview')" wire:navigate>
                            {{ __('Overview') }}
                        </flux:navlist.item>
                        <flux:navlist.item :href="route('supplier.orders')" :current="request()->routeIs('supplier.orders')" wire:navigate>
                            {{ __('Orders') }}
                        </flux:navlist.item>
                        <flux:navlist.item :href="route('supplier.resources')" :current="request()->routeIs('supplier.resources')" wire:navigate>
                            {{ __('Resources') }}
                        </flux:navlist.item>
                    </flux:navlist.group>
                </flux:navlist>
            @endif

             @if ($role === 'HR Manager')
                <flux:navlist variant="outline">
                    <flux:navlist.group class="grid">
                        <flux:navlist.item :href="route('hr_manager.overview')" :current="request()->routeIs('hr_manager.overview')" wire:navigate>
                            {{ __('Overview') }}
                        </flux:navlist.item>
                        <flux:navlist.item :href="route('hr_manager.employees')" :current="request()->routeIs('hr_manager.employees')" wire:navigate>
                            {{ __('Employees') }}
                        </flux:navlist.item>
                    </flux:navlist.group>
                </flux:navlist>
            @endif

            @if ($role === 'Production Manager')
                <flux:navlist variant="outline">
                    <flux:navlist.group class="grid">
                        <flux:navlist.item :href="route('production_manager.overview')" :current="request()->routeIs('production_manager.overview')" wire:navigate>
                            {{ __('Overview') }}
                        </flux:navlist.item>
                        <flux:navlist.item :href="route('production_manager.inventory')" :current="request()->routeIs('production_manager.inventory')" wire:navigate>
                            {{ __('Inventory') }}
                        </flux:navlist.item>
                        <flux:navlist.item :href="route('production_manager.orders')" :current="request()->routeIs('production_manager.orders')" wire:navigate>
                            {{ __('Orders') }}
                        </flux:navlist.item>
                         <flux:navlist.item :href="route('production_manager.production')" :current="request()->routeIs('production_manager.production')" wire:navigate>
                            {{ __('Production') }}
                        </flux:navlist.item>
                    </flux:navlist.group>
                </flux:navlist>
            @endif
            <flux:navlist variant="outline">
                <flux:navlist.group class="grid">
                    <flux:navlist.item :href="route('communication')" :current="request()->routeIs('communication')" wire:navigate>{{ __('Communication') }}</flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />

            <flux:navlist variant="outline">
                <!--<flux:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                {{ __('Repository') }}
                </flux:navlist.item>

                <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                {{ __('Documentation') }}
                </flux:navlist.item>-->
            </flux:navlist>

            <!-- Desktop User Menu -->
            <flux:dropdown class="hidden lg:block" position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon:trailing="chevrons-up-down"
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
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

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
        </flux:header>

        {{ $slot }}

        @stack('scripts')
        @fluxScripts
    </body>
</html>
