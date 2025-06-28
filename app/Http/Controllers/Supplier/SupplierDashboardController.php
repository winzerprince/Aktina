<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupplierDashboardController extends Controller
{
    //

    /**
     * Display the overview dashboard
     * @return \Illuminate\View\View
     */

    public function overview(){
        return view('supplier.overview');
    }

    /**
     * Display the orders dashboard
     * @return \Illuminate\View\View
     */

    public function orders(){
        return view('supplier.orders');
    }

    /**
     * Display the resources dashboard
     * @return \Illuminate\View\View
     */

    public function resources(){
        return view('supplier.resources');
    }
}
