<x-layouts.app>
    <x-header title="Vendor Management" subtitle="Manage vendor applications and verification status">
        <x-slot:middle class="!justify-end">
            <x-button icon="o-arrow-path" class="btn-circle btn-sm btn-ghost" tooltip="Refresh" wire:click="$refresh" />
        </x-slot:middle>
    </x-header>

    <div class="p-6">
        <livewire:admin.vendor-applications-table />
    </div>
</x-layouts.app>
