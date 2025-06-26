<x-layouts.app>
    <x-header title="Pending Sign-ups" subtitle="Manage users awaiting email verification">
        <x-slot:middle class="!justify-end">
            <x-button icon="o-arrow-path" class="btn-circle btn-sm btn-ghost" tooltip="Refresh" />
        </x-slot:middle>
    </x-header>

    <div class="px-4 py-6">
        <livewire:admin.user-management.pending-sign-ups.table />
    </div>
</x-layouts.app>
