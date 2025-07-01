<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Services\ApplicationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function __construct(
        private ApplicationService $applicationService
    ) {}

    /**
     * Display a listing of applications.
     */
    public function index(): View
    {
        $applications = Application::with('vendor.user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.applications.index', compact('applications'));
    }

    /**
     * Display the specified application.
     */
    public function show(Application $application): View
    {
        $application->load('vendor.user');
        
        return view('admin.applications.show', compact('application'));
    }

    /**
     * Schedule a meeting for the application.
     */
    public function schedule(Request $request, Application $application): RedirectResponse
    {
        $request->validate([
            'meeting_date' => 'required|date|after:now',
        ]);

        $success = $this->applicationService->scheduleMeeting(
            $application, 
            $request->input('meeting_date')
        );

        if ($success) {
            return redirect()->back()->with('success', 'Meeting scheduled successfully.');
        }

        return redirect()->back()->with('error', 'Failed to schedule meeting.');
    }

    /**
     * Complete a meeting for the application.
     */
    public function completeMeeting(Request $request, Application $application): RedirectResponse
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $success = $this->applicationService->completeMeeting(
            $application, 
            $request->input('notes')
        );

        if ($success) {
            return redirect()->back()->with('success', 'Meeting completed successfully.');
        }

        return redirect()->back()->with('error', 'Failed to complete meeting.');
    }

    /**
     * Approve the application.
     */
    public function approve(Application $application): RedirectResponse
    {
        $success = $this->applicationService->approveApplication($application);

        if ($success) {
            return redirect()->back()->with('success', 'Application approved successfully.');
        }

        return redirect()->back()->with('error', 'Failed to approve application.');
    }

    /**
     * Reject the application.
     */
    public function reject(Request $request, Application $application): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $success = $this->applicationService->rejectApplication(
            $application, 
            $request->input('rejection_reason')
        );

        if ($success) {
            return redirect()->back()->with('success', 'Application rejected successfully.');
        }

        return redirect()->back()->with('error', 'Failed to reject application.');
    }
}
