<x-layouts.app>
    <div class="container mx-auto py-8">
        <h1 class="text-2xl font-bold mb-4">HR Manager Dashboard</h1>
        <p>Welcome, {{ auth()->user()->name }}! You are logged in as an HR Manager.</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-dashboard-card title="Total Employees">
                154 Employees
            </x-dashboard-card>

            <x-dashboard-card title="Departments">
                6 Active Departments
            </x-dashboard-card>

            <x-dashboard-card title="Leave Requests">
                12 Pending Approvals
            </x-dashboard-card>
        </div>
    </div>
</x-layouts.app>

