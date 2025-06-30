<?php

namespace App\Livewire\Components;

use Livewire\Component;

class ProgressIndicator extends Component
{
    public $steps = [];
    public $currentStep = 0;
    public $showLabels = true;
    public $orientation = 'horizontal'; // 'horizontal' or 'vertical'

    public function mount($steps, $currentStep = 0, $showLabels = true, $orientation = 'horizontal')
    {
        $this->steps = $steps;
        $this->currentStep = $currentStep;
        $this->showLabels = $showLabels;
        $this->orientation = $orientation;
    }

    public function getStepStatus($stepIndex)
    {
        if ($stepIndex < $this->currentStep) {
            return 'completed';
        } elseif ($stepIndex === $this->currentStep) {
            return 'current';
        } else {
            return 'upcoming';
        }
    }

    public function render()
    {
        return view('livewire.components.progress-indicator');
    }
}
