<x-layouts.app>
    <x-header title="Sales Dashboard" subtitle="Manage and monitor sales from production managers">
        <x-slot:middle class="!justify-end">
            <x-button icon="o-arrow-path" class="btn-circle btn-sm btn-ghost" tooltip="Refresh" />
        </x-slot:middle>
    </x-header>

    <div class="px-4 py-6">
        <livewire:admin.sales.table />
    </div>
</x-layouts.app>
