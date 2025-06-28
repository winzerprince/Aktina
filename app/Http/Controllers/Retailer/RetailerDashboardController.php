<?php

namespace App\Http\Controllers\Retailer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RetailerDashboardController extends Controller
{
    //
    //

    /**
     * Display the overview dashboard
     * @return View
     */
    public function overview(){
        return view('retailer.overview');
    }


    /**
     * Display the orders dashboard
     * @return View
     */
    public function orders(){
        return view('retailer.orders');
    }

    /**
     * Display the sales dashbaord
     * @return View
     */
    public function sales(){
        return view('retailer.sales');
    }

    /**
     * Display the inventory page
     * @return View
     */
    public function inventory(){
        return view('retailer.inventory');
    }
    /**
     * Display the ratings dashboard
     * @return View
     */
    public function ratings(){
        return view('retailer.ratings');
    }

}
