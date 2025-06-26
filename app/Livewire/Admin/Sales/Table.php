<?php

namespace App\Livewire\Admin\Sales;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductionManager;
use App\Models\User;
use Livewire\Component;
use Carbon\Carbon;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    public function render()
    {
        // Get user IDs from the production_managers table
        $productionManagerUserIds = ProductionManager::pluck('user_id')->toArray();

        // Query orders where the seller_id is in the list of production manager user IDs
        $orders = Order::query()
            ->whereIn('seller_id', $productionManagerUserIds)
            ->with(['buyer', 'seller'])
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('livewire.admin.sales.table', [
            'orders' => $orders
        ]);
    }
}
