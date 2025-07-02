<?php

namespace App\Livewire\Communication;

use App\Interfaces\Services\ConversationServiceInterface;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;

class ConversationList extends Component
{
    public $conversations = [];
    public $selectedConversationId = null;

    public function __construct()
    {
        $this->loadConversations();
    }

    public function mount()
    {
        $this->loadConversations();
    }

    #[On('conversation-updated')]
    public function loadConversations()
    {
        $conversationService = app(ConversationServiceInterface::class);
        $this->conversations = $conversationService->getUserConversations(auth()->id())->toArray();
    }

    public function selectConversation($conversationId)
    {
        $this->selectedConversationId = $conversationId;
        $this->dispatch('conversation-selected', conversationId: $conversationId);
    }

    public function startNewConversation($userId)
    {
        $conversationService = app(ConversationServiceInterface::class);
        
        try {
            $conversation = $conversationService->createConversation([
                'user1_id' => auth()->id(),
                'user2_id' => $userId
            ]);
            
            $this->selectedConversationId = $conversation->id;
            $this->loadConversations();
            $this->dispatch('conversation-selected', conversationId: $conversation->id);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to start conversation: ' . $e->getMessage());
        }
    }

    public function getAvailableUsers()
    {
        $currentUser = auth()->user();
        $role = $currentUser->role;
        
        // Define communication rules based on supply chain relationships
        $allowedRoles = match($role) {
            'retailer' => ['vendor'],
            'vendor' => ['retailer', 'admin', 'production_manager', 'hr_manager'],
            'admin', 'production_manager', 'hr_manager' => ['vendor', 'supplier'],
            'supplier' => ['admin', 'production_manager', 'hr_manager'],
            default => []
        };
        
        return User::whereIn('role', $allowedRoles)
            ->where('id', '!=', auth()->id())
            ->select('id', 'name', 'role', 'company_name')
            ->get();
    }

    public function render()
    {
        return view('livewire.communication.conversation-list', [
            'availableUsers' => $this->getAvailableUsers()
        ]);
    }
}
