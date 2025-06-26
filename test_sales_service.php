<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Get the service
    $service = app(\App\Interfaces\Services\SalesAnalyticsServiceInterface::class);

    // Test with current date range (like the component would use)
    $startDate = \Carbon\Carbon::now()->subWeeks(12)->format('Y-m-d');
    $endDate = \Carbon\Carbon::now()->format('Y-m-d');

    echo "Testing with date range: {$startDate} to {$endDate}" . PHP_EOL;

    $data = $service->getSalesTrendsByTimeRange('week', $startDate, $endDate);

    echo "=== CURRENT RANGE SALES DATA ===" . PHP_EOL;
    echo "Total Sales: " . $data['total_sales'] . PHP_EOL;
    echo "Total Orders: " . $data['total_orders'] . PHP_EOL;
    echo "Average Order Value: " . $data['average_order_value'] . PHP_EOL;
    echo "Data Points: " . count($data['data']) . PHP_EOL;
    echo "Categories: " . count($data['categories']) . PHP_EOL;

    if (!empty($data['error'])) {
        echo "ERROR: " . $data['error'] . PHP_EOL;
    }

    if (!empty($data['data'])) {
        echo "Data points with sales (y > 0):" . PHP_EOL;
        $salesPoints = array_filter($data['data'], fn($point) => $point['y'] > 0);
        foreach ($salesPoints as $point) {
            echo "  " . json_encode($point) . PHP_EOL;
        }
        echo "Total weeks with sales: " . count($salesPoints) . PHP_EOL;

        // Show first and last few data points to see the range
        echo "First 3 data points:" . PHP_EOL;
        foreach (array_slice($data['data'], 0, 3) as $point) {
            echo "  " . json_encode($point) . PHP_EOL;
        }
        echo "Last 3 data points:" . PHP_EOL;
        foreach (array_slice($data['data'], -3) as $point) {
            echo "  " . json_encode($point) . PHP_EOL;
        }
    }

    echo PHP_EOL . "=== PRODUCTION MANAGER CHECK ===" . PHP_EOL;
    $pmIds = \App\Models\ProductionManager::pluck('user_id')->toArray();
    echo "PM User IDs: " . implode(', ', $pmIds) . PHP_EOL;

    $pmOrders = \App\Models\Order::whereIn('seller_id', $pmIds)
        ->whereBetween('created_at', ['2020-01-01', '2030-12-31'])
        ->count();
    echo "Orders from PMs in date range: " . $pmOrders . PHP_EOL;

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
    echo "Trace: " . $e->getTraceAsString() . PHP_EOL;
}
