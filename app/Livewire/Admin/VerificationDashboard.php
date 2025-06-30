<?php

namespace App\Livewire\Admin;

use App\Models\Application;
use App\Models\User;
use Livewire\Component;

class VerificationDashboard extends Component
{
    public $stats = [];

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $this->stats = [
            'pending_applications' => Application::where('status', 'pending')->count(),
            'scored_applications' => Application::where('status', 'scored')->count(),
            'meetings_scheduled' => Application::where('status', 'meeting_scheduled')->count(),
            'meetings_completed' => Application::where('status', 'meeting_completed')->count(),
            'total_applications' => Application::count(),
            'unverified_users' => User::where('verified', false)->where('role', '!=', 'admin')->count(),
            'recent_applications' => Application::with(['vendor.user'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(),
        ];
    }

    public function refresh()
    {
        $this->loadStats();
        $this->dispatch('dashboard-refreshed');
    }

    public function render()
    {
        return view('livewire.admin.verification-dashboard');
    }
}
