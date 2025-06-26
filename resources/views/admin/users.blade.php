<x-layouts.app>
    <x-header title="User Management" subtitle="Manage and monitor all system users">
        <x-slot:middle class="!justify-end">
            <x-button icon="o-arrow-path" class="btn-circle btn-sm btn-ghost" tooltip="Refresh" />
        </x-slot:middle>
    </x-header>

    <div class="px-4 py-6">
        <livewire:admin.user-management.users.table />
    </div>
</x-layouts.app>
