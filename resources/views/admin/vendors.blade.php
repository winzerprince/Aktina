<x-layouts.app>
    <x-header title="Vendor Management" subtitle="Manage vendor applications and verification status">
        <x-slot:middle class="!justify-end">
            <x-button icon="o-arrow-path" class="btn-circle btn-sm btn-ghost" tooltip="Refresh" />
        </x-slot:middle>
    </x-header>

    <div class="px-4 py-6">
        <livewire:admin.user-management.vendors.table />
    </div>
</x-layouts.app>
