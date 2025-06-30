<?php

namespace App\Livewire\Components;

use Livewire\Component;

class StatusBadge extends Component
{
    public $status;
    public $size = 'default'; // 'small', 'default', 'large'
    public $showIcon = true;

    public function mount($status, $size = 'default', $showIcon = true)
    {
        $this->status = $status;
        $this->size = $size;
        $this->showIcon = $showIcon;
    }

    public function getStatusConfig()
    {
        $configs = [
            'pending' => [
                'color' => 'yellow',
                'label' => 'Pending',
                'icon' => 'clock'
            ],
            'scored' => [
                'color' => 'blue',
                'label' => 'Scored',
                'icon' => 'chart-bar'
            ],
            'meeting_scheduled' => [
                'color' => 'purple',
                'label' => 'Meeting Scheduled',
                'icon' => 'calendar'
            ],
            'meeting_completed' => [
                'color' => 'indigo',
                'label' => 'Meeting Completed',
                'icon' => 'check-circle'
            ],
            'approved' => [
                'color' => 'green',
                'label' => 'Approved',
                'icon' => 'check'
            ],
            'rejected' => [
                'color' => 'red',
                'label' => 'Rejected',
                'icon' => 'x'
            ],
            'verified' => [
                'color' => 'green',
                'label' => 'Verified',
                'icon' => 'shield-check'
            ],
            'unverified' => [
                'color' => 'gray',
                'label' => 'Unverified',
                'icon' => 'shield-exclamation'
            ],
            'active' => [
                'color' => 'green',
                'label' => 'Active',
                'icon' => 'check-circle'
            ],
            'inactive' => [
                'color' => 'gray',
                'label' => 'Inactive',
                'icon' => 'minus-circle'
            ],
        ];

        return $configs[$this->status] ?? [
            'color' => 'gray',
            'label' => ucfirst(str_replace('_', ' ', $this->status)),
            'icon' => 'information-circle'
        ];
    }

    public function getSizeClasses()
    {
        return match ($this->size) {
            'small' => 'px-2 py-0.5 text-xs',
            'large' => 'px-4 py-2 text-base',
            default => 'px-2.5 py-0.5 text-sm',
        };
    }

    public function getIconSize()
    {
        return match ($this->size) {
            'small' => 'w-3 h-3',
            'large' => 'w-5 h-5',
            default => 'w-4 h-4',
        };
    }

    public function render()
    {
        return view('livewire.components.status-badge');
    }
}
