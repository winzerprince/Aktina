<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Retailer;
use App\Models\Supplier;
use App\Models\Vendor;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{

    /**
     * Display the admin dashboard overview.
     *
     * @return \Illuminate\View\View
     */
    public function overview()
    {
        return view('admin.overview');
    }

    /**
     * Display the sales dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function sales()
    {
       return view('admin.sales');
    }

    /**
     * Display the users dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function users()
    {
       return view('admin.users');
    }

    /**
     * Display the vendors dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function vendors()
    {

        return view('admin.vendors');

    }

    /**
     * Display the Pending signups dashboard.
     * @return \Illuminate\View\View
     */
    public function pendingSignups(){
        return view('admin.pending-signups');
    }

    /**
     * Display Trends and Predictions dashboard.
     * @return \Illuminate\View\View
     */
    public function trendsAndPredictions(){
        return view('admin.trends-and-predictions');
    }

    /**
     * Display the Important Metrics dashboard.
     * @return \Illuminate\View\View
     */
    public function importantMetrics()
    {
        return view('admin.important-metrics');
    }

    /**
     * Display the Customer Insights dashboard.
     * @return \Illuminate\View\View
     */
    public function customerInsights()
    {
        return view('admin.customer-insights');
    }

    /**
     * Display the orders management dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function orders()
    {
       return view('admin.orders');
    }
}
