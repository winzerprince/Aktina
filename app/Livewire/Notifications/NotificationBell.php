<?php

namespace App\Livewire\Notifications;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationBell extends Component
{
    public $unreadCount = 0;
    public $recentNotifications = [];
    public $showDropdown = false;

    protected $listeners = ['notification-read' => 'refreshCounts', 'all-notifications-read' => 'refreshCounts'];

    public function mount()
    {
        $this->refreshCounts();
    }

    public function refreshCounts()
    {
        $user = Auth::user();
        if ($user) {
            $this->unreadCount = $user->unreadNotifications()->count();
            $this->recentNotifications = $user->notifications()
                ->latest()
                ->take(5)
                ->get()
                ->toArray();
        }
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function markAsRead($notificationId)
    {
        $user = Auth::user();
        if ($user) {
            $notification = $user->notifications()->find($notificationId);
            if ($notification) {
                $notification->markAsRead();
                $this->refreshCounts();
                $this->dispatch('notification-read');
            }
        }
    }

    public function render()
    {
        return view('livewire.notifications.notification-bell');
    }
}
