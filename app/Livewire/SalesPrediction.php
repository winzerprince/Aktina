<?php

namespace App\Livewire;

use App\Services\MLService;
use Livewire\Component;

class SalesPrediction extends Component
{
    public $forecastData = [];
    public $isLoading = true;
    public $serviceError = false;
    public $horizon = 90;

    protected $queryString = ['horizon'];

    public function mount(MLService $mlService)
    {
        $this->loadForecastData($mlService);
    }

    public function loadForecastData(MLService $mlService)
    {
        $this->isLoading = true;
        $this->serviceError = false;

        if (!$mlService->isServiceHealthy()) {
            $this->serviceError = true;
            $this->isLoading = false;
            return;
        }

        $this->forecastData = $mlService->getSalesForecastChartData($this->horizon);
        $this->isLoading = false;
    }

    public function updatedHorizon()
    {
        $this->loadForecastData(app(MLService::class));
    }

    public function render()
    {
        return view('livewire.sales-prediction');
    }
}
