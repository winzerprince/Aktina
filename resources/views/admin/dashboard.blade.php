<x-layouts.app>
    <div class="container mx-auto py-8">
        <h1 class="text-2xl font-bold mb-4">Admin Dashboard</h1>
        <p>Welcome, {{ auth()->user()->name }}! You are logged in as an admin.</p>
        <!-- Add admin-specific content here -->
    </div>
</x-layouts.app>
