<!-- Modern Table Component -->
@props([
    'headers' => [],
    'striped' => true,
    'hoverable' => true,
])

<div class="overflow-hidden bg-white rounded-lg shadow">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            @if(!empty($headers))
                <thead class="bg-gray-50">
                    <tr>
                        @foreach($headers as $header)
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ $header }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
            @endif
            <tbody class="bg-white divide-y divide-gray-200">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>

<style>
@if($striped)
    tbody tr:nth-child(even) {
        background-color: rgba(249, 250, 251, 0.5);
    }
@endif

@if($hoverable)
    tbody tr:hover {
        background-color: rgba(249, 250, 251, 1);
    }
@endif
</style>
