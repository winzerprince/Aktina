<div class="bg-white dark:bg-gray-800 rounded-lg shadow-full flex flex-col">
    @if($conversation)
        <!-- Chat Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-600">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center">
                        <span class="text-sm font-medium text-white">
                            {{ substr($otherParticipant->name ?? 'U', 0, 1) }}
                        </span>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $otherParticipant->name ?? 'User' }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            @if($otherParticipant)
                                {{ ucfirst($otherParticipant->role ?? '') }}{{ $otherParticipant->company_name ? ' - ' . $otherParticipant->company_name : '' }}
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        <span class="w-2 h-2 mr-1.5 bg-green-400 rounded-full"></span>
                        Online
                    </span>
                </div>
            </div>
        </div>

        <!-- Messages Area -->
        <div id="message-container"
             class="flex-1 overflow-y-auto p-6 space-y-4"
             style="max-height: 500px;"
             wire:poll.10s="refreshMessages"
             x-data="{
                 scrollToBottom() {
                     const container = document.getElementById('message-container');
                     if (container) {
                         container.scrollTop = container.scrollHeight;
                     }
                 }
             }"
             x-init="$nextTick(() => scrollToBottom())"
             x-on:message-received.window="scrollToBottom()">
            @if($isLoading)
                <div class="flex justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-500"></div>
                    <span class="ml-2 text-sm text-gray-500 dark:text-gray-300">Loading messages...</span>
                </div>
            @elseif(empty($messages))
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-300">No messages yet</p>
                    <p class="text-xs text-gray-400 dark:text-gray-400">Start the conversation by sending a message</p>
                </div>
            @else
                @foreach($messages as $message)
                <div wire:key="message-{{ $message['id'] }}" class="flex {{ $message['sender_id'] == auth()->id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-xs lg:max-w-md {{ $message['sender_id'] == auth()->id() ? 'order-1' : 'order-2' }}">
                        <div class="px-4 py-2 rounded-lg {{ $message['sender_id'] == auth()->id() ? 'bg-indigo-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-200' }}">
                            <p class="text-sm">{{ $message['content'] }}</p>

                            <!-- File attachments -->
                            @if(isset($message['files']) && !empty($message['files']))
                            <div class="mt-2 space-y-1">
                                @foreach($message['files'] as $file)
                                <div class="flex items-center space-x-2 p-2 {{ $message['sender_id'] == auth()->id() ? 'bg-indigo-600/20' : 'bg-black/10 dark:bg-white/10' }} rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                    <button wire:click="downloadAttachment({{ $file['id'] }})"
                                            class="text-xs underline hover:no-underline {{ $message['sender_id'] == auth()->id() ? 'text-white' : 'text-gray-900 dark:text-gray-200' }}">
                                        {{ $file['original_name'] ?? 'Download' }}
                                    </button>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>

                        <div class="flex items-center mt-1 {{ $message['sender_id'] == auth()->id() ? 'justify-end' : 'justify-start' }}">
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($message['created_at'])->format('M j, g:i A') }}
                            </span>

                            @if($message['sender_id'] == auth()->id())
                            <button wire:click="deleteMessage({{ $message['id'] }})"
                                    class="ml-2 text-xs text-red-500 hover:text-red-700"
                                    onclick="return confirm('Are you sure you want to delete this message?')">
                                Delete
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>

        <!-- Message Input -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-600">
            <form wire:submit.prevent="sendMessage" class="space-y-3">
                <!-- File attachments preview -->
                @if(!empty($attachments))
                <div class="flex flex-wrap gap-2">
                    @foreach($attachments as $index => $attachment)
                    <div class="flex items-center space-x-2 bg-gray-100 dark:bg-gray-700 rounded px-3 py-1">
                        <span class="text-sm text-gray-600 dark:text-gray-200">{{ $attachment->getClientOriginalName() }}</span>
                        <button type="button" wire:click="$set('attachments.{{ $index }}', null)" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    @endforeach
                </div>
                @endif

                <div class="flex space-x-3">
                    <!-- File upload button -->
                    <label class="cursor-pointer inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-500 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                        </svg>
                        <input type="file" wire:model="attachments" multiple class="hidden" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.txt">
                    </label>

                    <!-- Message input -->
                    <div class="flex-1">
                        <textarea wire:model.live="newMessage"
                                placeholder="Type your message..."
                                rows="2"
                                class="block w-full border-gray-300 dark:border-gray-500 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 bg-white text-gray-900 dark:bg-gray-700 dark:text-gray-200 placeholder-gray-500 dark:placeholder-gray-400 resize-none @error('newMessage') border-red-300 dark:border-red-500 @enderror"
                                @keydown.ctrl.enter="$wire.sendMessage()"></textarea>
                        @error('newMessage')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Send button -->
                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                        <svg wire:loading.remove class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        <svg wire:loading class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </button>
                </div>

                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Press Ctrl+Enter to send • Max file size: 10MB
                </p>
            </form>
        </div>
    @else
        <!-- No conversation selected -->
        <div class="flex-1 flex items-center justify-center p-8">
            <div class="text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Select a conversation</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-300">Choose a conversation from the list to start messaging</p>
            </div>
        </div>
    @endif
</div>
