<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

use App\Interfaces\Services\SalesAnalyticsServiceInterface;

// Test the sales analytics service
$service = $app->make(SalesAnalyticsServiceInterface::class);

echo "Testing SalesAnalyticsService...\n";

try {
    $data = $service->getSalesTrendsByTimeRange('week', '2024-12-26', '2025-06-26');

    echo "✅ Service working!\n";
    echo "Total Sales: " . $data['total_sales'] . "\n";
    echo "Total Orders: " . $data['total_orders'] . "\n";
    echo "Data Points: " . count($data['data']) . "\n";

    if (!empty($data['data'])) {
        echo "Sample Data Point: \n";
        print_r($data['data'][0]);
    }

    echo "Categories: \n";
    print_r(array_slice($data['categories'], 0, 3));

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
