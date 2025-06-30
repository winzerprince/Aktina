<!-- Modern Dashboard Layout Component -->
<!-- Inspired by modern SaaS dashboard designs with clean aesthetics -->

<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo/Brand -->
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <img class="h-8 w-auto" src="{{ asset('images/aktina-logo.svg') }}" alt="Aktina SCM">
                    </div>
                    <div class="hidden md:block ml-4">
                        <h1 class="text-xl font-semibold text-gray-900">Supply Chain Management</h1>
                    </div>
                </div>

                <!-- Navigation & Profile -->
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <button class="p-2 rounded-lg text-gray-400 hover:text-gray-500 hover:bg-gray-100 transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-3.5-3.5c-.9-.9-.9-2.1 0-3L17 10a2 2 0 00-1.4-3.4L10 8a2 2 0 00-3.4 1.4l.6.6c.9.9.9 2.1 0 3L6 14H1"/>
                        </svg>
                    </button>

                    <!-- User Menu -->
                    <div class="relative">
                        <button class="flex items-center p-2 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
                            </div>
                            <span class="ml-2 text-sm font-medium hidden sm:block">{{ auth()->user()->name ?? 'User' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        @if (isset($title) || isset($subtitle) || isset($actions))
        <div class="mb-8">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div>
                    @if (isset($title))
                        <h1 class="text-2xl font-bold text-gray-900">{{ $title }}</h1>
                    @endif
                    @if (isset($subtitle))
                        <p class="mt-1 text-sm text-gray-500">{{ $subtitle }}</p>
                    @endif
                </div>
                @if (isset($actions))
                    <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                        {{ $actions }}
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Content -->
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <p class="text-sm text-gray-500">© {{ date('Y') }} Aktina Technologies. All rights reserved.</p>
                <div class="flex space-x-6">
                    <a href="#" class="text-sm text-gray-500 hover:text-gray-900 transition-colors duration-200">Support</a>
                    <a href="#" class="text-sm text-gray-500 hover:text-gray-900 transition-colors duration-200">Documentation</a>
                </div>
            </div>
        </div>
    </footer>
</div>
