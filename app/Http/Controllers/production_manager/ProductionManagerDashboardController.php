<?php

namespace App\Http\Controllers\production_manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductionManagerDashboardController extends Controller
{
    /**
     * Display the Production manager dashboard overview
     * @return \Illuminate\View\View
     */
    public function overview(){
        return view('production_manager.overview');
    }

    /**
     * Display the inventory dashboard
     * @return \Illuminate\View\View
     */
    public function inventory(){
        return view('production_manager.inventory');
    }

    /**
     * Display the orders dashboard
     * @return \Illuminate\View\View
     */
    public function orders(){
        return view('production_manager.orders');
    }

    /**
     * Display the production dashboard
     * @return \Illuminate\View\View
     */
    public function production(){
        return view('production_manager.production');
    }
}
