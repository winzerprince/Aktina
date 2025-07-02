<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Resource;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductionEfficiencyService
{
    public function getOverallEfficiency($timeframe)
    {
        // Calculate overall production efficiency based on multiple factors
        $plannedOutput = $this->getPlannedOutput($timeframe);
        $actualOutput = $this->getActualOutput($timeframe);
        
        if ($plannedOutput == 0) return 0;
        
        return round(($actualOutput / $plannedOutput) * 100, 1);
    }

    public function getProductionRate($timeframe)
    {
        // Units produced per hour
        $totalUnits = $this->getActualOutput($timeframe);
        $totalHours = $this->getProductionHours($timeframe);
        
        if ($totalHours == 0) return 0;
        
        return round($totalUnits / $totalHours, 1);
    }

    public function getQualityScore($timeframe)
    {
        // Quality score based on defect rates, returns, etc.
        $totalProduced = $this->getActualOutput($timeframe);
        $defectiveUnits = $this->getDefectiveUnits($timeframe);
        
        if ($totalProduced == 0) return 100;
        
        return round(((($totalProduced - $defectiveUnits) / $totalProduced) * 100), 1);
    }

    public function getDowntimeHours($timeframe)
    {
        // Calculate equipment downtime
        // In a real system, this would come from maintenance logs
        $totalPlannedHours = $this->getPlannedProductionHours($timeframe);
        $actualProductionHours = $this->getProductionHours($timeframe);
        
        return max(0, $totalPlannedHours - $actualProductionHours);
    }

    public function getThroughput($timeframe)
    {
        // Calculate throughput (completed orders)
        return Order::where('status', 'completed')
                   ->where('completed_at', '>=', $timeframe)
                   ->count();
    }

    public function getEfficiencyTrend($timeframe)
    {
        $days = $timeframe->diffInDays(now());
        $trend = collect();
        
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayStart = $date->startOfDay();
            $dayEnd = $date->endOfDay();
            
            $efficiency = $this->getOverallEfficiency($dayStart);
            
            $trend->push([
                'date' => $date->format('M j'),
                'efficiency' => $efficiency
            ]);
        }
        
        return $trend->toArray();
    }

    public function getLaborHours($timeframe)
    {
        // Calculate total labor hours
        // This would typically come from time tracking systems
        $workingDays = $timeframe->diffInWeekdays(now());
        $avgHoursPerDay = 8; // Standard working hours
        $avgWorkersPerDay = 25; // Average number of workers
        
        return $workingDays * $avgHoursPerDay * $avgWorkersPerDay;
    }

    public function getEnergyConsumption($timeframe)
    {
        // Energy consumption metrics
        // This would come from energy monitoring systems
        $days = $timeframe->diffInDays(now());
        
        return [
            'total_kwh' => $days * mt_rand(800, 1200), // Simulated daily consumption
            'cost' => $days * mt_rand(150, 300), // Simulated daily cost
            'efficiency_rating' => mt_rand(75, 95), // Energy efficiency rating
        ];
    }

    public function getCostBreakdown($timeframe)
    {
        // Production cost breakdown
        $totalCost = $this->getTotalProductionCost($timeframe);
        
        return [
            'materials' => round($totalCost * 0.45, 2),
            'labor' => round($totalCost * 0.35, 2),
            'overhead' => round($totalCost * 0.15, 2),
            'utilities' => round($totalCost * 0.05, 2),
            'total' => $totalCost
        ];
    }

    public function getUtilizationRate($timeframe)
    {
        // Equipment/facility utilization rate
        $plannedHours = $this->getPlannedProductionHours($timeframe);
        $actualHours = $this->getProductionHours($timeframe);
        
        if ($plannedHours == 0) return 0;
        
        return round(($actualHours / $plannedHours) * 100, 1);
    }

    public function getWastePercentage($timeframe)
    {
        // Calculate waste percentage
        $totalMaterials = $this->getTotalMaterialsUsed($timeframe);
        $wastedMaterials = $this->getWastedMaterials($timeframe);
        
        if ($totalMaterials == 0) return 0;
        
        return round(($wastedMaterials / $totalMaterials) * 100, 1);
    }

    public function getProductionAlerts()
    {
        return [
            [
                'id' => 'prod_001',
                'type' => 'efficiency',
                'severity' => 'warning',
                'title' => 'Production Efficiency Below Target',
                'message' => 'Current efficiency at 78%, target is 85%',
                'timestamp' => now()->subMinutes(15)->diffForHumans(),
                'acknowledged' => false
            ],
            [
                'id' => 'prod_002',
                'type' => 'equipment',
                'severity' => 'critical',
                'title' => 'Equipment Maintenance Required',
                'message' => 'Machine #3 scheduled for maintenance in 2 hours',
                'timestamp' => now()->subMinutes(45)->diffForHumans(),
                'acknowledged' => false
            ]
        ];
    }

    public function getQualityAlerts()
    {
        return [
            [
                'id' => 'qual_001',
                'type' => 'quality',
                'severity' => 'warning',
                'title' => 'Quality Score Decline',
                'message' => 'Quality score dropped to 89%, investigating batch #Q2024-001',
                'timestamp' => now()->subHours(2)->diffForHumans(),
                'acknowledged' => false
            ]
        ];
    }

    public function getMaintenanceAlerts()
    {
        return [
            [
                'id' => 'maint_001',
                'type' => 'maintenance',
                'severity' => 'info',
                'title' => 'Scheduled Maintenance',
                'message' => 'Weekly maintenance scheduled for tomorrow 6 AM',
                'timestamp' => now()->subHours(1)->diffForHumans(),
                'acknowledged' => true
            ]
        ];
    }

    public function acknowledgeAlert($alertId)
    {
        // In a real system, this would update an alerts table
        logger()->info("Production alert acknowledged", [
            'alert_id' => $alertId,
            'acknowledged_by' => auth()->id(),
            'timestamp' => now()
        ]);
        
        return true;
    }

    public function resolveAlert($alertId)
    {
        // In a real system, this would update an alerts table
        logger()->info("Production alert resolved", [
            'alert_id' => $alertId,
            'resolved_by' => auth()->id(),
            'timestamp' => now()
        ]);
        
        return true;
    }

    public function exportReport($filters)
    {
        // Export production report
        logger()->info("Production report export requested", [
            'filters' => $filters,
            'requested_by' => auth()->id()
        ]);
        
        return true;
    }

    // Private helper methods
    private function getPlannedOutput($timeframe)
    {
        // Get planned production output for the timeframe
        // This would come from production schedules
        $days = $timeframe->diffInDays(now());
        return $days * mt_rand(800, 1200); // Simulated daily target
    }

    private function getActualOutput($timeframe)
    {
        // Get actual production output
        return Order::where('status', 'completed')
                   ->where('completed_at', '>=', $timeframe)
                   ->sum('quantity');
    }

    private function getProductionHours($timeframe)
    {
        // Get actual production hours
        $days = $timeframe->diffInWeekdays(now());
        return $days * mt_rand(16, 20); // Simulated daily production hours
    }

    private function getPlannedProductionHours($timeframe)
    {
        // Get planned production hours
        $days = $timeframe->diffInWeekdays(now());
        return $days * 20; // Planned 20 hours per day
    }

    private function getDefectiveUnits($timeframe)
    {
        // Get number of defective units
        $totalProduced = $this->getActualOutput($timeframe);
        return round($totalProduced * (mt_rand(2, 8) / 100)); // 2-8% defect rate
    }

    private function getTotalProductionCost($timeframe)
    {
        // Calculate total production cost
        $days = $timeframe->diffInDays(now());
        return $days * mt_rand(15000, 25000); // Simulated daily cost
    }

    private function getTotalMaterialsUsed($timeframe)
    {
        // Get total materials used
        return Resource::where('updated_at', '>=', $timeframe)
                      ->sum('consumed_quantity');
    }

    private function getWastedMaterials($timeframe)
    {
        // Get wasted materials
        $totalUsed = $this->getTotalMaterialsUsed($timeframe);
        return round($totalUsed * (mt_rand(3, 7) / 100)); // 3-7% waste rate
    }
}
