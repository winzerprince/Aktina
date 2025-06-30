<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\ApplicationServiceInterface;
use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function __construct(
        private ApplicationServiceInterface $applicationService
    ) {
    }

    public function index()
    {
        return view('admin.applications.index');
    }

    public function show(Application $application)
    {
        return view('admin.applications.show', compact('application'));
    }

    public function schedule(Request $request, Application $application)
    {
        $request->validate([
            'meeting_date' => 'required|date|after:now',
        ]);

        $this->applicationService->scheduleMeeting(
            $application,
            $request->meeting_date
        );

        return redirect()->back()->with('success', 'Meeting scheduled successfully!');
    }

    public function completeMeeting(Request $request, Application $application)
    {
        $request->validate([
            'meeting_notes' => 'required|string|max:1000',
        ]);

        $this->applicationService->completeMeeting(
            $application,
            $request->meeting_notes
        );

        return redirect()->back()->with('success', 'Meeting completed successfully!');
    }

    public function approve(Application $application)
    {
        $this->applicationService->approveApplication($application);
        return redirect()->back()->with('success', 'Application approved successfully!');
    }

    public function reject(Request $request, Application $application)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $this->applicationService->rejectApplication($application, $request->reason);
        return redirect()->back()->with('success', 'Application rejected.');
    }
}
