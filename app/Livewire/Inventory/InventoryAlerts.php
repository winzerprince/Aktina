<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use Livewire\WithPagination;
use App\Interfaces\Services\AlertServiceInterface;
use App\Models\InventoryAlert;

class InventoryAlerts extends Component
{
    use WithPagination;

    public $alertType = 'all'; // all, low_stock, overstock, expired
    public $priority = 'all'; // all, low, medium, high, critical
    public $status = 'all'; // all, active, resolved, dismissed
    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $selectedAlerts = [];
    public $selectAll = false;

    protected $alertService;

    public function boot(AlertServiceInterface $alertService)
    {
        $this->alertService = $alertService;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedAlerts = $this->getAlerts()->pluck('id')->toArray();
        } else {
            $this->selectedAlerts = [];
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function resolveAlert($alertId)
    {
        try {
            $this->alertService->resolveAlert($alertId);
            session()->flash('success', 'Alert resolved successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to resolve alert: ' . $e->getMessage());
        }
    }

    public function dismissAlert($alertId)
    {
        try {
            $this->alertService->dismissAlert($alertId);
            session()->flash('success', 'Alert dismissed successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to dismiss alert: ' . $e->getMessage());
        }
    }

    public function bulkResolveAlerts()
    {
        if (empty($this->selectedAlerts)) {
            session()->flash('error', 'No alerts selected.');
            return;
        }

        try {
            $this->alertService->bulkResolveAlerts($this->selectedAlerts);
            session()->flash('success', count($this->selectedAlerts) . ' alerts resolved successfully!');
            $this->selectedAlerts = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to resolve alerts: ' . $e->getMessage());
        }
    }

    public function bulkDismissAlerts()
    {
        if (empty($this->selectedAlerts)) {
            session()->flash('error', 'No alerts selected.');
            return;
        }

        try {
            $this->alertService->bulkDismissAlerts($this->selectedAlerts);
            session()->flash('success', count($this->selectedAlerts) . ' alerts dismissed successfully!');
            $this->selectedAlerts = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to dismiss alerts: ' . $e->getMessage());
        }
    }

    public function getAlertStats()
    {
        return $this->alertService->getAlertStatistics();
    }

    private function getAlerts()
    {
        return InventoryAlert::with(['resource', 'warehouse'])
            ->when($this->alertType !== 'all', function ($query) {
                return $query->where('alert_type', $this->alertType);
            })
            ->when($this->priority !== 'all', function ($query) {
                return $query->where('priority', $this->priority);
            })
            ->when($this->status !== 'all', function ($query) {
                return $query->where('status', $this->status);
            })
            ->when($this->search, function ($query) {
                return $query->whereHas('resource', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })->orWhere('message', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        $alerts = $this->getAlerts()->paginate(15);
        $stats = $this->getAlertStats();

        return view('livewire.inventory.inventory-alerts', [
            'alerts' => $alerts,
            'stats' => $stats
        ]);
    }
}
