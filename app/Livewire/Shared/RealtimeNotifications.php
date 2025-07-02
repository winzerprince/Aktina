<?php

namespace App\Livewire\Shared;

use App\Services\RealtimeDataService;
use Livewire\Component;

class RealtimeNotifications extends Component
{
    public $notifications = [];
    public $unreadCount = 0;
    public $showDropdown = false;

    protected $listeners = ['notificationRead' => 'markAsRead'];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $realtimeService = app(RealtimeDataService::class);
        $data = $realtimeService->getRealtimeNotifications(auth()->id());
        
        $this->notifications = array_merge(
            $data['inventory_alerts'],
            $data['order_notifications'],
            $data['system_alerts']
        );
        
        $this->unreadCount = $data['unread_count'];
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function markAsRead($notificationId)
    {
        // Mark notification as read logic
        $this->unreadCount = max(0, $this->unreadCount - 1);
    }

    public function markAllAsRead()
    {
        $this->unreadCount = 0;
        $this->notifications = array_map(function ($notification) {
            $notification['read'] = true;
            return $notification;
        }, $this->notifications);
    }

    public function render()
    {
        return view('livewire.shared.realtime-notifications');
    }
}
