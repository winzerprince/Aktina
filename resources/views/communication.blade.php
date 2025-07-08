<x-layouts.app>
    <!-- Custom styles for inputs to ensure text visibility -->
    @push('styles')
    <style>
        /* Ensure text is always visible in textarea/inputs regardless of mode */
        .dark textarea, .dark input[type="text"], .dark [contenteditable] {
            color: #e5e7eb !important; /* text-gray-200 equivalent */
            background-color: #374151 !important; /* dark:bg-gray-700 equivalent */
        }

        textarea, input[type="text"], [contenteditable] {
            color: #111827 !important; /* text-gray-900 equivalent */
            background-color: #ffffff !important; /* bg-white equivalent */
        }
    </style>
    @endpush

    <h1 class="text-2xl font-bold mb-4">Communication</h1>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
                <!-- Left sidebar with tabs for contacts and conversations -->
                <div class="w-full md:w-1/3">
                    <div class="bg-white shadow-md rounded-lg overflow-hidden">
                        <div x-data="{ activeTab: 'conversations' }" class="border-b border-gray-200">
                            <div class="flex">
                                <button x-on:click="activeTab = 'conversations'"
                                        class="flex-1 py-3 px-4 text-center border-b-2"
                                        :class="activeTab === 'conversations' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'">
                                    Conversations
                                </button>
                                <button x-on:click="activeTab = 'contacts'"
                                        class="flex-1 py-3 px-4 text-center border-b-2"
                                        :class="activeTab === 'contacts' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'">
                                    Contacts
                                </button>
                            </div>

                            <div>
                                <div x-show="activeTab === 'conversations'">
                                    @livewire('communication.conversation-list')
                                </div>
                                <div x-show="activeTab === 'contacts'" x-cloak>
                                    @livewire('communication.contact-list')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right side - message thread -->
                <div class="w-full md:w-2/3 h-[80vh]">
                    @livewire('communication.message-thread', ['conversationId' => null], key('message-thread'))
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

@push('scripts')
<script>
    // Add any JavaScript needed for the chat interface
    document.addEventListener('DOMContentLoaded', function() {
        // Debounced event handler for message events
        let messageEventTimeout;

        const scrollMessages = () => {
            const container = document.getElementById('message-container');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        };

        // Handle auto-scrolling when new messages arrive (with debounce)
        Livewire.on('messageSent', () => {
            clearTimeout(messageEventTimeout);
            messageEventTimeout = setTimeout(scrollMessages, 100);
        });

        Livewire.on('messageReceived', () => {
            clearTimeout(messageEventTimeout);
            messageEventTimeout = setTimeout(scrollMessages, 100);
        });

        // Prevent Livewire from making duplicate requests when navigating
        Livewire.hook('request', ({ succeed }) => {
            succeed(({ snapshot, queryParams }) => {
                // Don't update Livewire components when they are not in focus/visible
                if (document.visibilityState === 'hidden') {
                    return false;
                }
            });
        });
    });
</script>
@endpush
