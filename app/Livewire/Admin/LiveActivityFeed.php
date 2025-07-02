<?php

namespace App\Livewire\Admin;

use App\Services\LiveActivityService;
use Livewire\Component;
use Livewire\Attributes\Computed;

class LiveActivityFeed extends Component
{
    public $refreshInterval = 2000; // 2 seconds
    public $maxActivities = 50;
    public $activityTypes = ['all'];
    public $showFilters = false;
    public $autoScroll = true;
    
    public function mount()
    {
        $this->dispatch('init-activity-feed');
    }

    #[Computed]
    public function activityService()
    {
        return app(LiveActivityService::class);
    }

    #[Computed]
    public function recentActivities()
    {
        return $this->activityService->getRecentActivities($this->maxActivities, $this->activityTypes);
    }

    #[Computed]
    public function activityStats()
    {
        return $this->activityService->getActivityStats();
    }

    #[Computed]
    public function userSessions()
    {
        return $this->activityService->getActiveSessions();
    }

    #[Computed]
    public function systemEvents()
    {
        return $this->activityService->getSystemEvents();
    }

    public function toggleActivityType($type)
    {
        if (in_array($type, $this->activityTypes)) {
            $this->activityTypes = array_diff($this->activityTypes, [$type]);
        } else {
            $this->activityTypes[] = $type;
        }
        
        if (empty($this->activityTypes)) {
            $this->activityTypes = ['all'];
        }
        
        $this->dispatch('activity-filter-changed', types: $this->activityTypes);
    }

    public function updateRefreshInterval($interval)
    {
        $this->refreshInterval = $interval;
        $this->dispatch('refresh-interval-updated', interval: $interval);
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function toggleAutoScroll()
    {
        $this->autoScroll = !$this->autoScroll;
        $this->dispatch('auto-scroll-toggled', enabled: $this->autoScroll);
    }

    public function clearActivities()
    {
        $this->activityService->clearActivities();
        $this->dispatch('activities-cleared');
    }

    public function exportActivities($format = 'csv')
    {
        $data = $this->activityService->exportActivities($this->activityTypes);
        
        $this->dispatch('download-activities', [
            'data' => $data,
            'format' => $format,
            'filename' => 'activity_feed_' . now()->format('Y-m-d_H-i-s')
        ]);
    }

    public function pauseActivity($activityId)
    {
        $this->activityService->pauseActivity($activityId);
        $this->dispatch('activity-paused', activityId: $activityId);
    }

    public function render()
    {
        return view('livewire.admin.live-activity-feed');
    }
}
