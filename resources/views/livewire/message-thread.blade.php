<div class="flex flex-col h-full bg-white rounded-lg shadow overflow-hidden">
    @if($conversation && $otherUser)
        <!-- Header -->
        <div class="flex items-center p-4 border-b border-gray-200">
            <div class="flex-shrink-0">
                <div class="relative">
                    <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-gray-700 font-semibold">
                        {{ substr($otherUser->name ?? 'U', 0, 1) }}
                    </div>
                    @if($otherUser->is_online)
                        <span class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full bg-green-500 ring-2 ring-white"></span>
                    @endif
                </div>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-900">{{ $otherUser->name }}</p>
                <p class="text-xs text-gray-500">
                    @if($otherUser->role)
                        {{ ucfirst($otherUser->role) }}
                    @endif
                </p>
            </div>
        </div>

        <!-- Messages -->
        <div class="flex-1 overflow-y-auto p-4 bg-gray-100 space-y-4" id="message-container" x-data="{}" x-init="setTimeout(() => $el.scrollTop = $el.scrollHeight, 100)">
            @if($messages && $messages->count() > 0)
                @foreach($messages as $msg)
                    <div class="flex {{ $msg->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                        <div class="{{ $msg->sender_id === auth()->id() ? 'bg-blue-500 text-white' : 'bg-white text-gray-800' }} rounded-lg px-4 py-2 max-w-xs sm:max-w-md shadow">
                            @if($msg->content)
                                <p class="text-sm break-words">{{ $msg->content }}</p>
                            @endif

                            @if($msg->files && $msg->files->count() > 0)
                                <div class="mt-2 space-y-2">
                                    @foreach($msg->files as $file)
                                        <div class="flex items-center p-2 bg-gray-50 rounded">
                                            <div class="flex-shrink-0 mr-2">
                                                @if(in_array($file->mime_type, ['image/jpeg', 'image/png', 'image/gif']))
                                                    <img src="{{ Storage::url($file->file_path) }}" alt="Image" class="w-12 h-12 object-cover rounded">
                                                @else
                                                    <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                @endif
                                            </div>
                                            <div class="flex-1 overflow-hidden">
                                                <p class="text-xs truncate {{ $msg->sender_id === auth()->id() ? 'text-gray-100' : 'text-gray-600' }}">
                                                    {{ $file->original_name }}
                                                </p>
                                                <p class="text-xs {{ $msg->sender_id === auth()->id() ? 'text-gray-200' : 'text-gray-500' }}">
                                                    {{ round($file->file_size / 1024, 2) }} KB
                                                </p>
                                            </div>
                                            <div>
                                                <button wire:click="downloadFile({{ $file->id }})" class="p-1 rounded hover:bg-gray-200">
                                                    <svg class="w-4 h-4 {{ $msg->sender_id === auth()->id() ? 'text-white' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <p class="text-xs mt-1 text-right {{ $msg->sender_id === auth()->id() ? 'text-gray-200' : 'text-gray-500' }}">
                                {{ $msg->created_at->format('h:i A') }}
                                @if($msg->sender_id === auth()->id())
                                    @if($msg->is_read)
                                        <span class="ml-1">✓✓</span>
                                    @else
                                        <span class="ml-1">✓</span>
                                    @endif
                                @endif
                            </p>
                        </div>
                    </div>
                @endforeach

                @if($messages->hasPages())
                    <div class="flex justify-center my-4">
                        {{ $messages->links() }}
                    </div>
                @endif
            @else
                <div class="flex items-center justify-center h-full">
                    <p class="text-gray-500 text-sm">No messages yet. Start a conversation!</p>
                </div>
            @endif
        </div>

        <!-- Message Input -->
        <div class="border-t border-gray-200 p-4 bg-white">
            <form wire:submit.prevent="sendMessage" class="flex flex-col space-y-2">
                <div class="flex space-x-2">
                    <div class="flex-1">
                        <textarea wire:model="message" placeholder="Type a message..." class="w-full border border-gray-300 rounded-md py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" rows="1"></textarea>
                    </div>
                    <div>
                        <button type="submit" class="inline-flex justify-center p-2 rounded-full text-blue-600 hover:bg-blue-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center">
                    <label for="file-upload" class="flex items-center cursor-pointer p-1 rounded-md hover:bg-gray-100">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                        </svg>
                        <span class="ml-1 text-sm text-gray-500">Attach File</span>
                    </label>
                    <input type="file" id="file-upload" wire:model="files" multiple class="hidden" />
                </div>

                <!-- File Preview -->
                @if(count($files) > 0)
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach($files as $index => $file)
                            <div class="relative p-2 bg-gray-100 rounded-md">
                                <button type="button" wire:click="removeFile({{ $index }})" class="absolute -top-2 -right-2 p-1 bg-red-500 text-white rounded-full">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span class="text-xs text-gray-600 truncate max-w-[100px]">{{ $file->getClientOriginalName() }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </form>
        </div>
    @else
        <div class="flex items-center justify-center h-full">
            <p class="text-gray-500">Select a conversation or start a new one</p>
        </div>
    @endif
</div>
