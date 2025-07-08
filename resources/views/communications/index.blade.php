@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
            <!-- Left sidebar with tabs for contacts and conversations -->
            <div class="w-full md:w-1/3">
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <div class="flex border-b border-gray-200">
                        <button x-data="{ tab: 'conversations' }"
                                x-on:click="tab = 'conversations'; $dispatch('tab-changed', { tab: 'conversations' })"
                                class="flex-1 py-3 px-4 text-center border-b-2"
                                :class="tab === 'conversations' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'">
                            Conversations
                        </button>
                        <button x-data="{ tab: 'contacts' }"
                                x-on:click="tab = 'contacts'; $dispatch('tab-changed', { tab: 'contacts' })"
                                class="flex-1 py-3 px-4 text-center border-b-2"
                                :class="tab === 'contacts' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'">
                            Contacts
                        </button>
                    </div>

                    <div x-data="{ activeTab: 'conversations' }" @tab-changed.window="activeTab = $event.detail.tab">
                        <div x-show="activeTab === 'conversations'">
                            @livewire('communication.conversation-list')
                        </div>
                        <div x-show="activeTab === 'contacts'">
                            @livewire('communication.contact-list')
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right side - message thread -->
            <div class="w-full md:w-2/3 h-[80vh]">
                @livewire('communication.message-thread')
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Add any JavaScript needed for the chat interface
    document.addEventListener('DOMContentLoaded', function() {
        // Handle auto-scrolling when new messages arrive
        Livewire.on('messageSent', () => {
            const container = document.getElementById('message-container');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        });

        Livewire.on('messageReceived', () => {
            const container = document.getElementById('message-container');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        });
    });
</script>
@endpush
