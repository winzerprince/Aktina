<x-layouts.app>
    <div class="container mx-auto py-8">
        {{-- <h1 class="text-2xl font-bold mb-4">Retailer Dashboard</h1> --}}
        <h1 class="text-2xl font-bold mb-4">Welcome, {{ auth()->user()->name }}!</h1>
        <!-- Add retailer-specific content here -->
    </div>
</x-layouts.app>
