<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Resource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ReportGeneratorService
{
    /**
     * Generate analytics report data
     */
    public function generateAnalyticsReport(string $type, array $filters = []): array
    {
        $startDate = Carbon::parse($filters['start_date'] ?? now()->subDays(30));
        $endDate = Carbon::parse($filters['end_date'] ?? now());
        $role = $filters['role'] ?? null;

        switch ($type) {
            case 'sales':
                return $this->generateSalesReport($startDate, $endDate, $role);
            case 'inventory':
                return $this->generateInventoryReport($startDate, $endDate);
            case 'users':
                return $this->generateUsersReport($startDate, $endDate, $role);
            case 'orders':
                return $this->generateOrdersReport($startDate, $endDate, $role);
            case 'production':
                return $this->generateProductionReport($startDate, $endDate);
            default:
                return $this->generateComprehensiveReport($startDate, $endDate);
        }
    }

    /**
     * Generate CSV format data
     */
    public function generateCSVData(string $type, array $filters = []): array
    {
        $reportData = $this->generateAnalyticsReport($type, $filters);
        
        return [
            'filename' => $this->generateFilename($type, 'csv'),
            'headers' => $this->getCSVHeaders($type),
            'data' => $this->formatDataForCSV($reportData, $type),
            'metadata' => [
                'generated_at' => now()->toISOString(),
                'report_type' => $type,
                'filters' => $filters,
                'total_records' => count($reportData['data'] ?? [])
            ]
        ];
    }

    /**
     * Generate PDF report structure
     */
    public function generatePDFData(string $type, array $filters = []): array
    {
        $reportData = $this->generateAnalyticsReport($type, $filters);
        
        return [
            'filename' => $this->generateFilename($type, 'pdf'),
            'title' => $this->getReportTitle($type),
            'data' => $reportData,
            'metadata' => [
                'generated_at' => now()->format('Y-m-d H:i:s'),
                'generated_by' => auth()->check() ? auth()->user()->name : 'System',
                'report_type' => ucfirst($type) . ' Report',
                'date_range' => $this->formatDateRange($filters),
                'total_records' => count($reportData['data'] ?? [])
            ],
            'charts' => $this->generateChartData($reportData, $type)
        ];
    }

    /**
     * Generate sales report
     */
    private function generateSalesReport(Carbon $startDate, Carbon $endDate, ?string $role): array
    {
        $query = Order::whereBetween('created_at', [$startDate, $endDate]);
        
        if ($role) {
            $query->whereHas('buyer', function ($q) use ($role) {
                $q->where('role', $role);
            });
        }

        $orders = $query->get();
        
        $totalRevenue = $orders->sum('total_amount');
        $totalOrders = $orders->count();
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        $dailySales = $orders->groupBy(function ($order) {
            return $order->created_at->format('Y-m-d');
        })->map(function ($dayOrders) {
            return [
                'date' => $dayOrders->first()->created_at->format('Y-m-d'),
                'orders_count' => $dayOrders->count(),
                'revenue' => $dayOrders->sum('total_amount'),
                'average_order_value' => $dayOrders->avg('total_amount') ?: 0
            ];
        })->values();

        return [
            'summary' => [
                'total_revenue' => $totalRevenue,
                'total_orders' => $totalOrders,
                'average_order_value' => round($averageOrderValue, 2),
                'period' => "{$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}"
            ],
            'data' => $dailySales->toArray(),
            'top_customers' => $this->getTopCustomers($orders),
            'sales_by_status' => $this->getSalesByStatus($orders)
        ];
    }

    /**
     * Generate inventory report
     */
    private function generateInventoryReport(Carbon $startDate, Carbon $endDate): array
    {
        $products = Product::all();
        $resources = Resource::with('inventoryMovements')->get();

        // For demonstration, we'll use a simplified approach
        $lowStockProducts = $products->filter(function ($product) {
            // Assuming stock level is stored directly in the product model
            return ($product->stock_quantity ?? 0) < 10;
        });

        $lowStockResources = $resources->filter(function ($resource) {
            // For resources, calculate quantity from the most recent movement
            $latestMovement = $resource->inventoryMovements()->latest()->first();
            $quantity = $latestMovement ? $latestMovement->quantity_after : 0;
            return $quantity < 5;
        });

        return [
            'summary' => [
                'total_products' => $products->count(),
                'total_resources' => $resources->count(),
                'low_stock_products' => $lowStockProducts->count(),
                'low_stock_resources' => $lowStockResources->count(),
                'total_inventory_value' => $this->calculateInventoryValue($products, $resources)
            ],
            'data' => [
                'products' => $products->map(function ($product) {
                    $stockQuantity = $product->stock_quantity ?? 0;
                    return [
                        'name' => $product->name ?? 'Product',
                        'category' => $product->category ?? 'Uncategorized',
                        'current_stock' => $stockQuantity,
                        'price' => $product->price ?? $product->msrp ?? 0,
                        'value' => $stockQuantity * ($product->price ?? $product->msrp ?? 0),
                        'status' => $this->getStockStatus($stockQuantity)
                    ];
                })->toArray(),
                'resources' => $resources->map(function ($resource) {
                    $latestMovement = $resource->inventoryMovements()->latest()->first();
                    $stockQuantity = $latestMovement ? $latestMovement->quantity_after : 0;
                    return [
                        'name' => $resource->name ?? 'Resource',
                        'unit' => $resource->unit ?? 'unit',
                        'current_stock' => $stockQuantity,
                        'cost_per_unit' => $resource->unit_cost ?? 0,
                        'total_value' => $stockQuantity * ($resource->unit_cost ?? 0),
                        'status' => $this->getStockStatus($stockQuantity)
                    ];
                })->toArray()
            ],
            'alerts' => [
                'low_stock_items' => $lowStockProducts->merge($lowStockResources)->count(),
                'out_of_stock_items' => $products->filter(function ($product) {
                    return ($product->stock_quantity ?? 0) === 0;
                })->count() + $resources->filter(function ($resource) {
                    $latestMovement = $resource->inventoryMovements()->latest()->first();
                    $quantity = $latestMovement ? $latestMovement->quantity_after : 0;
                    return $quantity === 0;
                })->count()
            ]
        ];
    }

    /**
     * Generate users report
     */
    private function generateUsersReport(Carbon $startDate, Carbon $endDate, ?string $role): array
    {
        $query = User::whereBetween('created_at', [$startDate, $endDate]);
        
        if ($role) {
            $query->where('role', $role);
        }

        $users = $query->get();
        $totalUsers = User::count();
        $activeUsers = User::where('email_verified_at', '!=', null)->count();

        $usersByRole = User::select('role', DB::raw('count(*) as count'))
            ->groupBy('role')
            ->get()
            ->pluck('count', 'role')
            ->toArray();

        $userGrowth = $users->groupBy(function ($user) {
            return $user->created_at->format('Y-m-d');
        })->map(function ($dayUsers, $date) {
            return [
                'date' => $date,
                'new_users' => $dayUsers->count(),
                'roles' => $dayUsers->countBy('role')->toArray()
            ];
        })->values();

        return [
            'summary' => [
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'new_users_period' => $users->count(),
                'verification_rate' => $totalUsers > 0 ? ($activeUsers / $totalUsers) * 100 : 0
            ],
            'data' => $userGrowth->toArray(),
            'distribution' => $usersByRole,
            'recent_users' => $users->take(10)->map(function ($user) {
                return [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'created_at' => $user->created_at->format('Y-m-d H:i'),
                    'verified' => $user->email_verified_at ? 'Yes' : 'No'
                ];
            })->toArray()
        ];
    }

    /**
     * Generate orders report
     */
    private function generateOrdersReport(Carbon $startDate, Carbon $endDate, ?string $role): array
    {
        $query = Order::whereBetween('created_at', [$startDate, $endDate]);
        
        if ($role) {
            // If role is specified, filter by buyer role
            $query->whereHas('buyer', function ($q) use ($role) {
                $q->where('role', $role);
            });
        }

        $orders = $query->get();

        $ordersByStatus = $orders->countBy('status')->toArray();
        $orderTrends = $orders->groupBy(function ($order) {
            return $order->created_at->format('Y-m-d');
        })->map(function ($dayOrders, $date) {
            return [
                'date' => $date,
                'orders_count' => $dayOrders->count(),
                'total_amount' => $dayOrders->sum('total_amount'),
                'statuses' => $dayOrders->countBy('status')->toArray()
            ];
        })->values();

        return [
            'summary' => [
                'total_orders' => $orders->count(),
                'total_value' => $orders->sum('total_amount'),
                'average_order_value' => $orders->avg('total_amount'),
                'pending_orders' => $orders->where('status', 'pending')->count(),
                'completed_orders' => $orders->where('status', 'completed')->count()
            ],
            'data' => $orderTrends->toArray(),
            'status_distribution' => $ordersByStatus,
            'recent_orders' => $orders->take(20)->map(function ($order) {
                return [
                    'id' => $order->id,
                    'customer' => $order->user->name,
                    'status' => $order->status,
                    'total_amount' => $order->total_amount,
                    'created_at' => $order->created_at->format('Y-m-d H:i')
                ];
            })->toArray()
        ];
    }

    /**
     * Generate production report
     */
    private function generateProductionReport(Carbon $startDate, Carbon $endDate): array
    {
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])->get();
        $totalOrders = $orders->count();
        $completedOrders = $orders->where('status', 'completed')->count();
        $processingOrders = $orders->where('status', 'processing')->count();

        $efficiency = $totalOrders > 0 ? ($completedOrders / $totalOrders) * 100 : 0;
        $fulfillmentRate = $totalOrders > 0 ? (($completedOrders + $processingOrders) / $totalOrders) * 100 : 0;

        return [
            'summary' => [
                'efficiency_rate' => round($efficiency, 2),
                'fulfillment_rate' => round($fulfillmentRate, 2),
                'total_orders' => $totalOrders,
                'completed_orders' => $completedOrders,
                'processing_orders' => $processingOrders,
                'pending_orders' => $orders->where('status', 'pending')->count()
            ],
            'data' => $orders->groupBy(function ($order) {
                return $order->created_at->format('Y-m-d');
            })->map(function ($dayOrders, $date) {
                $dayTotal = $dayOrders->count();
                $dayCompleted = $dayOrders->where('status', 'completed')->count();
                
                return [
                    'date' => $date,
                    'total_orders' => $dayTotal,
                    'completed_orders' => $dayCompleted,
                    'efficiency' => $dayTotal > 0 ? ($dayCompleted / $dayTotal) * 100 : 0
                ];
            })->values()->toArray()
        ];
    }

    /**
     * Generate comprehensive report
     */
    private function generateComprehensiveReport(Carbon $startDate, Carbon $endDate): array
    {
        return [
            'sales' => $this->generateSalesReport($startDate, $endDate, null),
            'inventory' => $this->generateInventoryReport($startDate, $endDate),
            'users' => $this->generateUsersReport($startDate, $endDate, null),
            'orders' => $this->generateOrdersReport($startDate, $endDate, null),
            'production' => $this->generateProductionReport($startDate, $endDate)
        ];
    }

    /**
     * Helper methods
     */
    private function generateFilename(string $type, string $format): string
    {
        return strtolower($type) . '_report_' . now()->format('Y_m_d_His') . '.' . $format;
    }

    private function getReportTitle(string $type): string
    {
        return ucfirst($type) . ' Analytics Report';
    }

    private function formatDateRange(array $filters): string
    {
        $start = isset($filters['start_date']) ? Carbon::parse($filters['start_date'])->format('Y-m-d') : 'N/A';
        $end = isset($filters['end_date']) ? Carbon::parse($filters['end_date'])->format('Y-m-d') : 'N/A';
        
        return "{$start} to {$end}";
    }

    private function getCSVHeaders(string $type): array
    {
        switch ($type) {
            case 'sales':
                return ['Date', 'Orders Count', 'Revenue', 'Average Order Value'];
            case 'inventory':
                return ['Item Name', 'Type', 'Current Stock', 'Value', 'Status'];
            case 'users':
                return ['Date', 'New Users', 'Total Active Users'];
            case 'orders':
                return ['Date', 'Orders Count', 'Total Amount', 'Status Distribution'];
            default:
                return ['Date', 'Metric', 'Value'];
        }
    }

    private function formatDataForCSV(array $reportData, string $type): array
    {
        if (!isset($reportData['data'])) {
            return [];
        }

        switch ($type) {
            case 'sales':
                return array_map(function ($item) {
                    return [
                        $item['date'],
                        $item['orders_count'],
                        $item['revenue'],
                        $item['average_order_value']
                    ];
                }, $reportData['data']);
            
            case 'inventory':
                $csvData = [];
                foreach ($reportData['data']['products'] as $product) {
                    $csvData[] = [
                        $product['name'],
                        'Product',
                        $product['current_stock'],
                        $product['value'],
                        $product['status']
                    ];
                }
                foreach ($reportData['data']['resources'] as $resource) {
                    $csvData[] = [
                        $resource['name'],
                        'Resource',
                        $resource['current_stock'],
                        $resource['total_value'],
                        $resource['status']
                    ];
                }
                return $csvData;
            
            default:
                return $reportData['data'];
        }
    }

    private function generateChartData(array $reportData, string $type): array
    {
        // Generate chart configurations for PDF reports
        switch ($type) {
            case 'sales':
                return [
                    'revenue_trend' => [
                        'type' => 'line',
                        'data' => $reportData['data'],
                        'x_field' => 'date',
                        'y_field' => 'revenue'
                    ]
                ];
            case 'orders':
                return [
                    'order_trend' => [
                        'type' => 'bar',
                        'data' => $reportData['data'],
                        'x_field' => 'date',
                        'y_field' => 'orders_count'
                    ]
                ];
            default:
                return [];
        }
    }

    private function getTopCustomers(Collection $orders): array
    {
        return $orders->groupBy('buyer_id')
            ->map(function ($customerOrders) {
                $buyer = $customerOrders->first()->buyer;
                return [
                    'buyer_id' => $customerOrders->first()->buyer_id,
                    'name' => $buyer->name ?? 'Unknown',
                    'orders_count' => $customerOrders->count(),
                    'total_spent' => $customerOrders->sum('total_amount')
                ];
            })
            ->sortByDesc('total_spent')
            ->take(10)
            ->values()
            ->toArray();
    }

    private function getSalesByStatus(Collection $orders): array
    {
        return $orders->countBy('status')->toArray();
    }

    private function calculateInventoryValue(Collection $products, Collection $resources): float
    {
        $productValue = $products->sum(function ($product) {
            return ($product->stock_quantity ?? 0) * ($product->price ?? $product->msrp ?? 0);
        });

        $resourceValue = $resources->sum(function ($resource) {
            $latestMovement = $resource->inventoryMovements()->latest()->first();
            $quantity = $latestMovement ? $latestMovement->quantity_after : 0;
            return $quantity * ($resource->unit_cost ?? 0);
        });

        return $productValue + $resourceValue;
    }

    private function getStockStatus(int $quantity): string
    {
        if ($quantity === 0) return 'out_of_stock';
        if ($quantity < 10) return 'low_stock';
        if ($quantity < 50) return 'medium_stock';
        return 'healthy_stock';
    }
}
