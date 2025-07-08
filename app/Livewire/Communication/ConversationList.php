<?php

namespace App\Livewire\Communication;

use App\Interfaces\Services\ConversationServiceInterface;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;

class ConversationList extends Component
{
    public $conversations = [];
    public $availableUsers = [];
    public $selectedConversationId = null;

    public function mount()
    {
        // Ensure user is authenticated before loading conversations
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $this->loadConversations();
        $this->loadAvailableUsers();
    }

    #[On('conversation-updated')]
    #[On('message-sent')]
    public function loadConversations()
    {
        $user = auth()->user();

        // Ensure user is authenticated and is a User instance
        if (!$user || !($user instanceof User)) {
            $this->conversations = [];
            return;
        }

        try {
            $conversationService = app(ConversationServiceInterface::class);
            $paginatedConversations = $conversationService->getUserConversations($user);

            // Transform conversations to include other participant data
            $this->conversations = $paginatedConversations->map(function ($conversation) use ($user) {
                $otherParticipant = $conversation->user_one_id === $user->id
                    ? $conversation->userTwo
                    : $conversation->userOne;

                return [
                    'id' => $conversation->id,
                    'other_participant' => [
                        'id' => $otherParticipant->id,
                        'name' => $otherParticipant->name,
                        'role' => $otherParticipant->role,
                        'company_name' => $otherParticipant->company_name ?? 'N/A',
                    ],
                    'last_message_at' => $conversation->last_message_at,
                    'created_at' => $conversation->created_at,
                    'has_unread' => false, // TODO: Implement unread message tracking
                ];
            })->toArray();
        } catch (\Exception $e) {
            $this->conversations = [];
            session()->flash('error', 'Failed to load conversations: ' . $e->getMessage());
        }
    }

    public function loadAvailableUsers()
    {
        $user = auth()->user();

        if (!$user || !($user instanceof User)) {
            $this->availableUsers = [];
            return;
        }

        $conversationService = app(ConversationServiceInterface::class);
        $contacts = $conversationService->getAvailableContacts($user);

        $this->availableUsers = collect($contacts)->map(function ($contact) {
            return [
                'id' => $contact->id,
                'name' => $contact->name,
                'role' => $contact->role,
                'company_name' => $contact->company_name,
            ];
        })->toArray();
    }

    public function selectConversation($conversationId)
    {
        // Prevent reselecting the same conversation
        if ($this->selectedConversationId == $conversationId) {
            return;
        }

        $this->selectedConversationId = $conversationId;
        $this->dispatch('conversation-selected', conversationId: $conversationId);
    }

    public function startNewConversation($userId)
    {
        $conversationService = app(ConversationServiceInterface::class);

        try {
            $conversation = $conversationService->createConversation([
                'user_one_id' => auth()->id(),
                'user_two_id' => $userId
            ]);

            $this->selectedConversationId = $conversation->id;
            $this->loadConversations();
            $this->dispatch('conversation-selected', conversationId: $conversation->id);

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to start conversation: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.communication.conversation-list');
    }
}
