<div class="overflow-hidden bg-white shadow-md rounded-lg">
    <div class="p-4 border-b border-gray-200">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
            </div>
            <input wire:model.live.debounce.300ms="search" type="text" class="block w-full py-2 pl-10 pr-3 text-sm border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Search contacts">
        </div>
    </div>
    <ul class="divide-y divide-gray-200 max-h-[70vh] overflow-y-auto">
        @forelse($contacts as $contact)
            <li class="cursor-pointer hover:bg-gray-50" wire:click="selectContact({{ $contact['id'] }})">
                <div class="flex items-center px-4 py-3">
                    <div class="flex-shrink-0">
                        <div class="relative">
                            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-gray-700 font-semibold">
                                {{ substr($contact['name'] ?? 'U', 0, 1) }}
                            </div>
                            @if(isset($contact['is_online']) && $contact['is_online'])
                                <span class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full bg-green-500 ring-2 ring-white"></span>
                            @endif
                        </div>
                    </div>
                    <div class="flex-1 min-w-0 ml-4">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-900 truncate">
                                {{ $contact['name'] }}
                            </p>
                        </div>
                        <div class="mt-1">
                            <p class="text-xs text-gray-500 truncate">
                                @if(isset($contact['role']))
                                    <span class="px-2 py-1 text-xs font-medium text-blue-800 bg-blue-100 rounded-full">
                                        {{ ucfirst($contact['role']) }}
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </li>
        @empty
            <li class="py-4 px-6 text-center text-gray-500">
                <p>No contacts available</p>
            </li>
        @endforelse
    </ul>

    {{-- Array contacts cannot be paginated directly --}}
    @if(is_object($contacts) && method_exists($contacts, 'hasPages') && $contacts->hasPages())
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $contacts->links() }}
        </div>
    @endif
</div>
