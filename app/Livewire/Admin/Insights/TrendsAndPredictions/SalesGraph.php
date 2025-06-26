<?php

namespace App\Livewire\Admin\Insights\TrendsAndPredictions;

use App\Models\Order;
use App\Models\ProductionManager;
use Livewire\Component;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class SalesGraph extends Component
{
    public string $timeRange = 'week';
    public string $startDate;
    public string $endDate;

    protected $listeners = ['refreshChart' => '$refresh'];

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
                $now->copy()->subDays(7)->format('Y-m-d'),
                $now->format('Y-m-d')
            ],
            'week' => [
                $now->copy()->subWeeks(12)->format('Y-m-d'),
                $now->format('Y-m-d')
            ],
            'month' => [
                $now->copy()->subMonths(12)->format('Y-m-d'),
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
        $this->dispatch('timeRangeUpdated', $this->getSalesDataProperty());
    }

    public function updatedEndDate(): void
    {
        $this->dispatch('timeRangeUpdated', $this->getSalesDataProperty());
    }

    public function refreshChart(): void
    {
        $this->dispatch('refreshChart', $this->getSalesDataProperty());
    }

    public function getSalesDataProperty(): array
    {
        return $this->timeRange === 'default'
            ? $this->getDefaultSalesData()
            : $this->getSalesDataByTimeRange($this->timeRange);
    }

    private function getProductionManagerOrders(Carbon $startDate, Carbon $endDate): \Illuminate\Database\Eloquent\Collection
    {
        // Get user IDs from the production_managers table
        $productionManagerUserIds = ProductionManager::pluck('user_id')->toArray();

        // Query orders where the seller_id is in the list of production manager user IDs
        return Order::query()
            ->whereIn('seller_id', $productionManagerUserIds)
            ->with(['buyer', 'seller'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
    }

    private function getSalesDataByTimeRange(string $timeRange): array
    {
        // Configuration for different time ranges
        $config = $this->getTimeRangeConfig($timeRange);

        // Parse dates based on configuration
        $startDate = Carbon::parse($this->startDate)->{$config['startDateMethod']}();
        $endDate = Carbon::parse($this->endDate)->{$config['endDateMethod']}();

        // Get orders for the period
        $orders = $this->getProductionManagerOrders($startDate, $endDate);

        // Group orders by the specified format
        $groupedOrders = $orders->groupBy(function($order) use ($config) {
            return $config['groupByCallback']($order->created_at);
        });

        // Generate period keys
        $periodKeys = CarbonPeriod::create(
                $startDate,
                $config['periodInterval'],
                $endDate
            )
            ->map($config['periodKeyCallback'])
            ->toArray();

        // Build chart data with the configured formatter
        return $this->buildChartData(
            $groupedOrders,
            $periodKeys,
            $config['labelFormatter']
        );
    }

    private function getTimeRangeConfig(string $timeRange): array
    {
        return match($timeRange) {
            'day' => [
                'startDateMethod' => 'startOfDay',
                'endDateMethod' => 'endOfDay',
                'groupByCallback' => fn($date) => $date->format('Y-m-d'),
                'periodInterval' => '1 day',
                'periodKeyCallback' => fn($date) => $date->format('Y-m-d'),
                'labelFormatter' => fn($date) => Carbon::parse($date)->format('M d')
            ],
            'week' => [
                'startDateMethod' => 'startOfWeek',
                'endDateMethod' => 'endOfWeek',
                'groupByCallback' => fn($date) => $date->format('Y') . '-W' . $date->format('W'),
                'periodInterval' => '1 week',
                'periodKeyCallback' => fn($date) => $date->format('Y') . '-W' . $date->format('W'),
                'labelFormatter' => fn($weekKey) => 'Week ' . explode('-W', $weekKey)[1]
            ],
            'month' => [
                'startDateMethod' => 'startOfMonth',
                'endDateMethod' => 'endOfMonth',
                'groupByCallback' => fn($date) => $date->format('Y-m'),
                'periodInterval' => '1 month',
                'periodKeyCallback' => fn($date) => $date->format('Y-m'),
                'labelFormatter' => fn($monthKey) => Carbon::createFromFormat('Y-m', $monthKey)->format('M Y')
            ],
            default => [
                'startDateMethod' => 'startOfDay',
                'endDateMethod' => 'endOfDay',
                'groupByCallback' => fn($date) => $date->format('Y-m-d'),
                'periodInterval' => '1 day',
                'periodKeyCallback' => fn($date) => $date->format('Y-m-d'),
                'labelFormatter' => fn($date) => Carbon::parse($date)->format('M d')
            ]
        };
    }

    private function getDefaultSalesData(): array
    {
        return [
            'data' => [],
            'categories' => [],
            'total_sales' => 0,
            'total_orders' => 0,
            'average_order_value' => 0
        ];
    }

    private function buildChartData($groupedOrders, array $periodKeys, callable $labelFormatter): array
    {
        $chartData = [];
        $categories = [];
        $totalSales = 0;
        $totalOrders = 0;

        foreach ($periodKeys as $key) {
            $orders = $groupedOrders->get($key, collect());
            $periodSales = $orders->sum('price');
            $periodOrderCount = $orders->count();

            $totalSales += $periodSales;
            $totalOrders += $periodOrderCount;

            $label = $labelFormatter($key);
            $categories[] = $label;

            $chartData[] = [
                'x' => $label,
                'y' => round($periodSales, 2)
            ];
        }

        return [
            'data' => $chartData,
            'categories' => $categories,
            'total_sales' => round($totalSales, 2),
            'total_orders' => $totalOrders,
            'average_order_value' => $totalOrders > 0 ? round($totalSales / $totalOrders, 2) : 0
        ];
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
