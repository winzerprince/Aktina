<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VendorDashboardController extends Controller
{
    //

    /**
     * Display the overview dashboard
     * @return \Illuminate\View\View
     */
    public function overview(){
        return view('vendor.overview');
    }


    /**
     * Display the orders dashboard
     * @return \Illuminate\View\View
     */
    public function orders(){
        return view('vendor.orders');
    }

    /**
     * Display the sales dashbaord
     * @return \Illuminate\View\View
     */
    public function sales(){
        return view('vendor.sales');
    }

    /**
     * Display the inventory page
     * @return \Illuminate\View\View
     */
    public function inventory(){
        return view('vendor.inventory');
    }

    /**
     * Display the retailers dashboard
     * @return \Illuminate\View\View
     */
    public function retailers(){
        return view('vendor.retailers');
    }
}
