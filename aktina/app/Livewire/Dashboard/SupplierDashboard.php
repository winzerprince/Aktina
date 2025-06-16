<?php

namespace App\Livewire\Dashboard;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class SupplierDashboard extends Component
{
    use WithPagination;

    public string $tab = 'home';
    public string $orderSearch = '';
    public string $orderStatus = '';
    public string $orderSort = 'created_at';
    public string $orderDirection = 'desc';

    // Profile settings
    public string $companyName = '';
    public string $contactPerson = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';
    public bool $emailNotifications = true;
    public bool $smsNotifications = false;

    public function mount()
    {
        $user = auth()->user();
        $this->companyName = $user->name;
        $this->email = $user->email;
        // Initialize other profile fields as needed
    }

    public function getOrdersProperty()
    {
        $query = Order::forSupplier(auth()->id())
            ->with('productionManager')
            ->when($this->orderSearch, function ($q) {
                $q->where(function ($query) {
                    $query->where('order_number', 'like', '%' . $this->orderSearch . '%')
                          ->orWhere('title', 'like', '%' . $this->orderSearch . '%')
                          ->orWhere('description', 'like', '%' . $this->orderSearch . '%');
                });
            })
            ->when($this->orderStatus, function ($q) {
                $q->where('status', $this->orderStatus);
            })
            ->orderBy($this->orderSort, $this->orderDirection);

        return $query->paginate(10);
    }

    public function getOrderStatsProperty()
    {
        $userId = auth()->id();

        return [
            'total' => Order::forSupplier($userId)->count(),
            'pending' => Order::forSupplier($userId)->byStatus('pending')->count(),
            'confirmed' => Order::forSupplier($userId)->byStatus('confirmed')->count(),
            'in_production' => Order::forSupplier($userId)->byStatus('in_production')->count(),
            'shipped' => Order::forSupplier($userId)->byStatus('shipped')->count(),
            'delivered' => Order::forSupplier($userId)->byStatus('delivered')->count(),
            'on_time_percentage' => $this->calculateOnTimeDelivery(),
        ];
    }

    public function getRecentOrdersProperty()
    {
        return Order::forSupplier(auth()->id())
            ->with('productionManager')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    private function calculateOnTimeDelivery()
    {
        $delivered = Order::forSupplier(auth()->id())
            ->byStatus('delivered')
            ->whereNotNull('delivery_date')
            ->get();

        if ($delivered->isEmpty()) {
            return 0;
        }

        $onTime = $delivered->filter(function ($order) {
            return $order->delivery_date <= $order->required_by;
        })->count();

        return round(($onTime / $delivered->count()) * 100);
    }

    public function updateOrderSort($column)
    {
        if ($this->orderSort === $column) {
            $this->orderDirection = $this->orderDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->orderSort = $column;
            $this->orderDirection = 'asc';
        }
    }

    public function clearOrderFilters()
    {
        $this->orderSearch = '';
        $this->orderStatus = '';
        $this->resetPage();
    }

    public function updateProfile()
    {
        $this->validate([
            'companyName' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();
        $user->update([
            'name' => $this->companyName,
            'email' => $this->email,
        ]);

        session()->flash('message', 'Profile updated successfully!');
    }

    public function render()
    {
        return view('livewire.dashboard.supplier-dashboard')
            ->layout('components.layouts.dashboard');
    }
}
