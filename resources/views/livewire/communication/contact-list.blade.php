<div class="h-80 overflow-y-auto">
    <!-- Search input -->
    <div class="p-4 border-b border-gray-200">
        <input wire:model.live.debounce.300ms="searchTerm"
               type="text"
               placeholder="Search contacts..."
               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-gray-200 bg-white dark:bg-gray-700">
    </div>

    <!-- Contacts list -->
    <div class="divide-y divide-gray-200">
        @forelse($allContacts as $contact)
            <div wire:click="startConversation({{ $contact->id }})"
                 class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors duration-200">
                <div class="flex items-center space-x-3">
                    <!-- Avatar -->
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-medium">
                            {{ strtoupper(substr($contact->name, 0, 1)) }}
                        </div>
                    </div>

                    <!-- Contact info -->
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                            {{ $contact->name }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                            {{ $contact->email }}
                        </p>
                        @if($contact->role)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                @if($contact->role === 'admin') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @elseif($contact->role === 'vendor') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($contact->role === 'retailer') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                                @endif">
                                {{ ucfirst($contact->role) }}
                            </span>
                        @endif
                    </div>

                    <!-- Start conversation button -->
                    <div class="flex-shrink-0">
                        <button class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM9 9a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No contacts available</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    @if($searchTerm)
                        No contacts match your search.
                    @else
                        You don't have any contacts to chat with yet.
                    @endif
                </p>
            </div>
        @endforelse
    </div>
</div>
