<?php

namespace App\Http\Controllers\hr_manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HrManagerDashboardController extends Controller
{
    /**
     * Display the overview dashboard
     *@return  View
     */
    public function overview()
    {
        return view('hr_manager.overview');
    }

    /**
     * Display the employees dashboard
     * @return View
     */
    public function employees()
    {
        return view('hr_manager.employees');
    }
}
