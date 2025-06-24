<div class="card border rounded-lg p-6 shadow bg-gray-100">
    <h3>{{ $title ?? 'Card Title' }}</h3>
    <div>
        <span class="text-blue-600 text-2xl font-bold">
            {{ $slot }}
        </span>
    </div>
</div>

