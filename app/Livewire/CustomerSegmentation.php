<?php

namespace App\Livewire;

use App\Services\MLService;
use Livewire\Component;

class CustomerSegmentation extends Component
{
    public $segmentData = [];
    public $isLoading = true;
    public $serviceError = false;

    public function mount(MLService $mlService)
    {
        $this->loadSegmentData($mlService);
    }

    public function loadSegmentData(MLService $mlService)
    {
        $this->isLoading = true;
        $this->serviceError = false;

        if (!$mlService->isServiceHealthy()) {
            $this->serviceError = true;
            $this->isLoading = false;
            return;
        }

        $this->segmentData = $mlService->getCustomerSegmentChartData();
        $this->isLoading = false;
    }

    public function render()
    {
        return view('livewire.customer-segmentation');
    }
}
