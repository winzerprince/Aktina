<?php

namespace App\Livewire\Communication;

use App\Interfaces\Services\ConversationServiceInterface;
use Livewire\Component;
use Livewire\Attributes\On;

class CommunicationDashboard extends Component
{
    public $unreadCount = 0;
    public $recentConversations = [];
    public $communicationStats = [];

    public function mount()
    {
        $this->loadDashboardData();
    }

    #[On('message-sent')]
    #[On('conversation-updated')]
    public function loadDashboardData()
    {
        $conversationService = app(ConversationServiceInterface::class);
        
        try {
            // Get unread message count
            $this->unreadCount = $conversationService->getUnreadMessageCount(auth()->id());
            
            // Get recent conversations
            $this->recentConversations = $conversationService->getUserConversations(auth()->id())
                ->take(5)
                ->toArray();
            
            // Get communication stats
            $this->communicationStats = [
                'total_conversations' => $conversationService->getUserConversations(auth()->id())->count(),
                'unread_messages' => $this->unreadCount,
                'active_conversations' => $conversationService->getActiveConversations(auth()->id())->count(),
                'messages_sent_today' => $conversationService->getMessagesSentToday(auth()->id())
            ];
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to load communication data: ' . $e->getMessage());
        }
    }

    public function markAllAsRead()
    {
        try {
            $conversationService = app(ConversationServiceInterface::class);
            $conversationService->markAllMessagesAsRead(auth()->id());
            
            $this->loadDashboardData();
            $this->dispatch('conversation-updated');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to mark messages as read: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.communication.communication-dashboard');
    }
}
