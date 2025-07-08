<?php

namespace App\Livewire\Communication;

use App\Interfaces\Services\ConversationServiceInterface;
use App\Models\User;
use App\Models\Conversation;
use Livewire\Component;

class ContactList extends Component
{
    public $contacts = [];
    public $searchTerm = '';
    public $existingConversations = [];

    public function mount()
    {
        // Ensure user is authenticated before loading contacts
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $this->loadContacts();
        $this->loadExistingConversations();
    }

    public function loadContacts()
    {
        $user = auth()->user();

        // Ensure user is authenticated and is a User instance
        if (!$user || !($user instanceof User)) {
            $this->contacts = [];
            return;
        }

        $conversationService = app(ConversationServiceInterface::class);
        $this->contacts = $conversationService->getAvailableContacts($user);
    }

    /**
     * Load all existing conversations to ensure we can access users who have messaged us
     * even if they're not in our "available contacts" list
     */
    public function loadExistingConversations()
    {
        $user = auth()->user();

        if (!$user || !($user instanceof User)) {
            $this->existingConversations = [];
            return;
        }

        // Get all existing conversations for the current user
        $conversations = Conversation::where('user_one_id', $user->id)
            ->orWhere('user_two_id', $user->id)
            ->with(['userOne', 'userTwo'])
            ->get();

        // Extract the other participants from these conversations
        $existingParticipants = [];
        foreach ($conversations as $conversation) {
            $otherUser = $conversation->user_one_id === $user->id ?
                $conversation->userTwo :
                $conversation->userOne;

            if ($otherUser) {
                $existingParticipants[$otherUser->id] = $otherUser;
            }
        }

        $this->existingConversations = $existingParticipants;
    }

    public function startConversation($contactId)
    {
        $user = auth()->user();
        $contact = User::find($contactId);

        if (!$user || !$contact) {
            return;
        }

        $conversationService = app(ConversationServiceInterface::class);
        $conversation = $conversationService->createOrFindConversation($user, $contact);

        if ($conversation) {
            // Dispatch event to select this conversation
            $this->dispatch('conversation-selected', conversationId: $conversation->id);
            // Refresh conversations list
            $this->dispatch('conversation-updated');
        }
    }

    public function updatedSearchTerm()
    {
        $this->loadContacts();
    }

    public function render()
    {
        // Combine regular contacts with any additional users from existing conversations
        $allContacts = collect($this->contacts);

        // Add users from existing conversations who aren't in the regular contacts list
        foreach ($this->existingConversations as $userId => $user) {
            if (!$allContacts->contains('id', $userId)) {
                $allContacts->push($user);
            }
        }

        // Filter contacts by search term if provided
        if ($this->searchTerm) {
            $searchTerm = strtolower($this->searchTerm);
            $allContacts = $allContacts->filter(function($contact) use ($searchTerm) {
                return str_contains(strtolower($contact->name), $searchTerm) ||
                       str_contains(strtolower($contact->email ?? ''), $searchTerm) ||
                       str_contains(strtolower($contact->company_name ?? ''), $searchTerm);
            });
        }

        return view('livewire.communication.contact-list', [
            'allContacts' => $allContacts
        ]);
    }
}
