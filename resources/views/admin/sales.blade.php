<x-layouts.app>
    <x-header title="Sales Dashboard" subtitle="Manage and monitor sales from production managers">
        <x-slot:middle class="!justify-end">
            <x-button icon="o-arrow-path" class="btn-circle btn-sm btn-ghost" tooltip="Refresh" />
        </x-slot:middle>
    </x-header>

    <div class="max-w-7xl mx-auto p-6 space-y-6">
        <livewire:sales.sales-table />
    </div>

</x-layouts.app>
