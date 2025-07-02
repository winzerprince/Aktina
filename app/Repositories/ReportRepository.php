<?php

namespace App\Repositories;

use App\Interfaces\Repositories\ReportRepositoryInterface;
use App\Models\Order;
use App\Models\Resource;
use App\Models\User;
use App\Models\SalesAnalytic;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportRepository implements ReportRepositoryInterface
{
    public function generateInventoryReport(Carbon $startDate, Carbon $endDate): array
    {
        return [
            'summary' => [
                'total_items' => Resource::count(),
                'total_value' => Resource::sum(DB::raw('quantity * price')),
                'low_stock_items' => Resource::where('quantity', '<=', DB::raw('minimum_stock_level'))->count(),
                'out_of_stock_items' => Resource::where('quantity', 0)->count()
            ],
            'by_category' => Resource::select('type')
                ->selectRaw('COUNT(*) as count')
                ->selectRaw('SUM(quantity * price) as value')
                ->selectRaw('SUM(quantity) as total_quantity')
                ->groupBy('type')
                ->get(),
            'movements' => DB::table('inventory_movements')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select('type')
                ->selectRaw('COUNT(*) as count')
                ->selectRaw('SUM(quantity) as total_quantity')
                ->groupBy('type')
                ->get(),
            'alerts' => DB::table('inventory_alerts')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select('type')
                ->selectRaw('COUNT(*) as count')
                ->groupBy('type')
                ->get()
        ];
    }

    public function generateSalesReport(Carbon $startDate, Carbon $endDate): array
    {
        return [
            'summary' => [
                'total_revenue' => SalesAnalytic::whereBetween('date', [$startDate, $endDate])->sum('revenue'),
                'total_orders' => SalesAnalytic::whereBetween('date', [$startDate, $endDate])->sum('orders_count'),
                'average_order_value' => SalesAnalytic::whereBetween('date', [$startDate, $endDate])->avg('average_order_value'),
                'conversion_rate' => SalesAnalytic::whereBetween('date', [$startDate, $endDate])->avg('conversion_rate')
            ],
            'daily_trends' => SalesAnalytic::whereBetween('date', [$startDate, $endDate])
                ->orderBy('date')
                ->get(['date', 'revenue', 'orders_count', 'average_order_value']),
            'top_products' => DB::table('order_resource')
                ->join('orders', 'order_resource.order_id', '=', 'orders.id')
                ->join('resources', 'order_resource.resource_id', '=', 'resources.id')
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->select('resources.name')
                ->selectRaw('SUM(order_resource.quantity) as total_sold')
                ->selectRaw('SUM(order_resource.quantity * order_resource.price) as total_revenue')
                ->groupBy('resources.id', 'resources.name')
                ->orderByDesc('total_revenue')
                ->limit(10)
                ->get()
        ];
    }

    public function generateOrderReport(Carbon $startDate, Carbon $endDate): array
    {
        return [
            'summary' => [
                'total_orders' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
                'pending_orders' => Order::where('status', 'pending')->whereBetween('created_at', [$startDate, $endDate])->count(),
                'approved_orders' => Order::where('status', 'approved')->whereBetween('created_at', [$startDate, $endDate])->count(),
                'rejected_orders' => Order::where('status', 'rejected')->whereBetween('created_at', [$startDate, $endDate])->count(),
                'total_value' => Order::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount')
            ],
            'by_status' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->select('status')
                ->selectRaw('COUNT(*) as count')
                ->selectRaw('SUM(total_amount) as total_value')
                ->groupBy('status')
                ->get(),
            'by_user' => Order::join('users', 'orders.user_id', '=', 'users.id')
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->select('users.name')
                ->selectRaw('COUNT(orders.id) as order_count')
                ->selectRaw('SUM(orders.total_amount) as total_value')
                ->groupBy('users.id', 'users.name')
                ->orderByDesc('total_value')
                ->get(),
            'daily_trends' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('DATE(created_at) as date')
                ->selectRaw('COUNT(*) as order_count')
                ->selectRaw('SUM(total_amount) as total_value')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
        ];
    }

    public function generateUserActivityReport(Carbon $startDate, Carbon $endDate): array
    {
        return [
            'summary' => [
                'total_users' => User::count(),
                'active_users' => User::whereBetween('updated_at', [$startDate, $endDate])->count(),
                'new_users' => User::whereBetween('created_at', [$startDate, $endDate])->count()
            ],
            'by_role' => User::select('role')
                ->selectRaw('COUNT(*) as count')
                ->groupBy('role')
                ->get(),
            'login_activity' => DB::table('users')
                ->whereBetween('updated_at', [$startDate, $endDate])
                ->selectRaw('DATE(updated_at) as date')
                ->selectRaw('COUNT(DISTINCT id) as active_users')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'order_activity' => User::join('orders', 'users.id', '=', 'orders.user_id')
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->select('users.name', 'users.role')
                ->selectRaw('COUNT(orders.id) as order_count')
                ->selectRaw('SUM(orders.total_amount) as total_spent')
                ->groupBy('users.id', 'users.name', 'users.role')
                ->orderByDesc('order_count')
                ->get()
        ];
    }

    public function generateFinancialReport(Carbon $startDate, Carbon $endDate): array
    {
        return [
            'summary' => [
                'total_revenue' => SalesAnalytic::whereBetween('date', [$startDate, $endDate])->sum('revenue'),
                'total_orders' => Order::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount'),
                'inventory_value' => Resource::sum(DB::raw('quantity * price')),
                'pending_order_value' => Order::where('status', 'pending')->sum('total_amount')
            ],
            'revenue_trends' => SalesAnalytic::whereBetween('date', [$startDate, $endDate])
                ->select('date', 'revenue')
                ->orderBy('date')
                ->get(),
            'cost_analysis' => [
                'inventory_costs' => Resource::sum(DB::raw('quantity * cost')),
                'operational_costs' => 0, // TODO: Implement when cost tracking is added
                'total_margin' => SalesAnalytic::whereBetween('date', [$startDate, $endDate])->sum('revenue') - Resource::sum(DB::raw('quantity * cost'))
            ]
        ];
    }

    public function getCustomReportData(array $parameters): array
    {
        $query = DB::table($parameters['table'] ?? 'orders');
        
        if (isset($parameters['joins'])) {
            foreach ($parameters['joins'] as $join) {
                $query->join($join['table'], $join['first'], $join['operator'], $join['second']);
            }
        }
        
        if (isset($parameters['where'])) {
            foreach ($parameters['where'] as $condition) {
                $query->where($condition['column'], $condition['operator'], $condition['value']);
            }
        }
        
        if (isset($parameters['date_range'])) {
            $query->whereBetween($parameters['date_range']['column'], [
                $parameters['date_range']['start'],
                $parameters['date_range']['end']
            ]);
        }
        
        if (isset($parameters['select'])) {
            $query->select($parameters['select']);
        }
        
        if (isset($parameters['group_by'])) {
            $query->groupBy($parameters['group_by']);
        }
        
        if (isset($parameters['order_by'])) {
            $query->orderBy($parameters['order_by']['column'], $parameters['order_by']['direction'] ?? 'asc');
        }
        
        if (isset($parameters['limit'])) {
            $query->limit($parameters['limit']);
        }
        
        return $query->get()->toArray();
    }

    public function exportReportData(array $data, string $format = 'csv'): string
    {
        if (empty($data)) {
            return '';
        }
        
        switch ($format) {
            case 'csv':
                return $this->exportToCsv($data);
            case 'json':
                return json_encode($data, JSON_PRETTY_PRINT);
            default:
                return $this->exportToCsv($data);
        }
    }

    private function exportToCsv(array $data): string
    {
        if (empty($data)) {
            return '';
        }
        
        $output = '';
        $headers = array_keys((array) $data[0]);
        $output .= implode(',', $headers) . "\n";
        
        foreach ($data as $row) {
            $values = array_values((array) $row);
            $output .= implode(',', array_map(function($value) {
                return '"' . str_replace('"', '""', $value) . '"';
            }, $values)) . "\n";
        }
        
        return $output;
    }
}
