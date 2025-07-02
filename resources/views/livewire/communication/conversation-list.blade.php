<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Conversations</h3>
        <button wire:click="loadConversations" 
                class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Refresh
        </button>
    </div>

    <!-- Start New Conversation -->
    <div class="mb-6">
        <div x-data="{ showNewConversation: false }" class="space-y-4">
            <button @click="showNewConversation = !showNewConversation"
                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                New Conversation
            </button>

            <div x-show="showNewConversation" x-transition class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Start conversation with:</h4>
                <div class="space-y-2">
                    @foreach($availableUsers as $user)
                    <button wire:click="startNewConversation({{ $user['id'] }})"
                            class="w-full text-left p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                                        {{ substr($user['name'], 0, 1) }}
                                    </span>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user['name'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ ucfirst($user['role']) }} - {{ $user['company_name'] }}</p>
                            </div>
                        </div>
                    </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Conversation List -->
    <div class="space-y-2">
        @if(empty($conversations))
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No conversations yet</p>
                <p class="text-xs text-gray-400 dark:text-gray-500">Start a new conversation to begin messaging</p>
            </div>
        @else
            @foreach($conversations as $conversation)
            <div wire:click="selectConversation({{ $conversation['id'] }})"
                 class="p-4 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 {{ $selectedConversationId == $conversation['id'] ? 'ring-2 ring-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : '' }}">
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center">
                                <span class="text-sm font-medium text-white">
                                    {{ substr($conversation['other_participant']['name'] ?? 'Unknown', 0, 1) }}
                                </span>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $conversation['other_participant']['name'] ?? 'Unknown User' }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ ucfirst($conversation['other_participant']['role'] ?? '') }} - {{ $conversation['other_participant']['company_name'] ?? '' }}
                            </p>
                            @if(isset($conversation['last_message']))
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 truncate">
                                {{ Str::limit($conversation['last_message']['content'] ?? '', 50) }}
                            </p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex flex-col items-end space-y-1">
                        @if(isset($conversation['last_message']))
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            {{ \Carbon\Carbon::parse($conversation['last_message']['created_at'])->diffForHumans() }}
                        </span>
                        @endif
                        
                        @if(isset($conversation['unread_count']) && $conversation['unread_count'] > 0)
                        <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                            {{ $conversation['unread_count'] }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        @endif
    </div>
</div>
