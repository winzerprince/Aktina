<?php

namespace App\Livewire\Admin\Insights\TrendsAndPredictions;

use App\Interfaces\Services\SalesAnalyticsServiceInterface;
use Livewire\Component;
use Carbon\Carbon;

class SalesGraph extends Component
{
    public string $timeRange = 'week';
    public string $startDate;
    public string $endDate;
    public array $filters = [];

    protected $listeners = ['refreshChart' => '$refresh'];

    protected SalesAnalyticsServiceInterface $salesService;

    public function boot(SalesAnalyticsServiceInterface $salesService): void
    {
        $this->salesService = $salesService;
    }

    public function mount(): void
    {
        $this->setDefaultDateRange();
    }

    public function updatedTimeRange(): void
    {
        $this->setDefaultDateRange();
        $this->dispatch('timeRangeUpdated', $this->getSalesDataProperty());
    }

    public function setDefaultDateRange(): void
    {
        $now = Carbon::now();

        [$this->startDate, $this->endDate] = match ($this->timeRange) {
            'day' => [
                $now->copy()->subDays(30)->format('Y-m-d'),  // Last 30 days
                $now->format('Y-m-d')
            ],
            'week' => [
                $now->copy()->subWeeks(12)->format('Y-m-d'), // Last 12 weeks
                $now->format('Y-m-d')
            ],
            'month' => [
                $now->copy()->subMonths(12)->format('Y-m-d'), // Last 12 months
                $now->format('Y-m-d')
            ],
            default => [
                $now->copy()->subDays(30)->format('Y-m-d'),
                $now->format('Y-m-d')
            ]
        };
    }

    public function updatedStartDate(): void
    {
        $this->dispatch('timeRangeUpdated', $this->getSalesData());
    }

    public function updatedEndDate(): void
    {
        $this->dispatch('timeRangeUpdated', $this->getSalesData());
    }

    public function refreshChart(): void
    {
        $this->dispatch('refreshChart', $this->getSalesData());
    }

    public function getSalesDataProperty(): array
    {
        try {
            return $this->salesService->getSalesTrendsByTimeRange(
                $this->timeRange,
                $this->startDate,
                $this->endDate,
                $this->filters
            );
        } catch (\Exception $e) {
            \Log::error('SalesGraph Error:', [
                'error' => $e->getMessage(),
                'timeRange' => $this->timeRange,
                'startDate' => $this->startDate,
                'endDate' => $this->endDate
            ]);

            return $this->salesService->getDefaultSalesData($e->getMessage());
        }
    }

    public function getTimeRangeOptionsProperty(): array
    {
        return [
            'day' => 'Daily (Last 7 days)',
            'week' => 'Weekly (Last 12 weeks)',
            'month' => 'Monthly (Last 12 months)'
        ];
    }

    public function render()
    {
        return view('livewire.admin.insights.trends-and-predictions.sales-graph');
    }
}
