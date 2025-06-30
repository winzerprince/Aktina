<?php

namespace App\Livewire\Verification;

use App\Interfaces\Services\ApplicationServiceInterface;
use App\Interfaces\Services\VerificationServiceInterface;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class VendorApplication extends Component
{
    use WithFileUploads;

    public $pdfFile;
    public $isUploading = false;
    public $application = null;
    public $verificationStatus = null;

    public function mount()
    {
        $this->loadApplicationStatus();
    }

    public function loadApplicationStatus()
    {
        $user = Auth::user();
        $vendor = $user->vendor;

        if ($vendor) {
            $applicationService = app(ApplicationServiceInterface::class);
            $this->application = $applicationService->getVendorApplicationStatus($vendor);
        }

        $verificationService = app(VerificationServiceInterface::class);
        $this->verificationStatus = $verificationService->getVerificationStatus($user);
    }

    public function uploadApplication()
    {
        $this->validate([
            'pdfFile' => 'required|file|mimes:pdf|max:10240', // 10MB max
        ]);

        try {
            $this->isUploading = true;

            $user = Auth::user();
            $vendor = $user->vendor;

            if (!$vendor) {
                $this->addError('general', 'Vendor profile not found. Please contact support.');
                return;
            }

            $applicationService = app(ApplicationServiceInterface::class);
            $application = $applicationService->submitApplication($vendor, $this->pdfFile);

            session()->flash('success', 'Application submitted successfully! Reference: ' . $application->application_reference);

            $this->reset('pdfFile');
            $this->loadApplicationStatus();

        } catch (\Exception $e) {
            $this->addError('general', 'Failed to submit application: ' . $e->getMessage());
        } finally {
            $this->isUploading = false;
        }
    }

    public function render()
    {
        return view('livewire.verification.vendor-application');
    }
}
