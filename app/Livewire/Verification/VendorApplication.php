<?php

namespace App\Livewire\Verification;

use App\Interfaces\Services\ApplicationServiceInterface;
use App\Interfaces\Services\VerificationServiceInterface;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;

class VendorApplication extends Component
{
    use WithFileUploads;

    #[Validate]
    public $pdfFile;

    public $application = null;
    public $verificationStatus = null;
    public $isUploading = false;
    public $uploadProgress = 0;

    protected function rules()
    {
        return [
            'pdfFile' => [
                'required',
                'file',
                'mimes:pdf',
                'max:10240', // 10MB max
                function ($attribute, $value, $fail) {
                    // Additional PDF validation
                    if ($value && $value->getClientOriginalExtension() !== 'pdf') {
                        $fail('The file must be a PDF document.');
                    }

                    // Check file size in bytes (additional check)
                    if ($value && $value->getSize() > 10485760) { // 10MB in bytes
                        $fail('The PDF file must not exceed 10MB.');
                    }

                    // Basic PDF header validation
                    if ($value) {
                        $handle = fopen($value->getRealPath(), 'r');
                        $header = fread($handle, 5);
                        fclose($handle);

                        if ($header !== '%PDF-') {
                            $fail('The file must be a valid PDF document.');
                        }
                    }
                }
            ]
        ];
    }

    protected $messages = [
        'pdfFile.required' => 'Please select a PDF file to upload.',
        'pdfFile.file' => 'The uploaded item must be a file.',
        'pdfFile.mimes' => 'Only PDF files are allowed.',
        'pdfFile.max' => 'The PDF file must not exceed 10MB.',
    ];

    public function mount(ApplicationServiceInterface $applicationService)
    {
        $this->application = $applicationService->getApplicationForUser(auth()->user());
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

    public function submitApplication(ApplicationServiceInterface $applicationService)
    {
        // Authorization check
        if (!auth()->user()->hasRole('vendor') || auth()->user()->is_verified) {
            $this->addError('authorization', 'You are not authorized to submit applications.');
            return;
        }

        // Validate the file
        $this->validate();

        try {
            $this->isUploading = true;

            // Security: Generate unique filename to prevent path traversal
            $filename = 'vendor_application_' . auth()->id() . '_' . time() . '.pdf';

            // Store file securely
            $path = $this->pdfFile->storeAs('vendor-applications', $filename, 'private');

            if (!$path) {
                throw new \Exception('Failed to store the uploaded file.');
            }

            // Create or update application
            $this->application = $applicationService->createOrUpdateApplication(
                auth()->user(),
                $path
            );

            $this->reset('pdfFile');
            $this->isUploading = false;

            session()->flash('success', 'Application submitted successfully! You will be notified once it has been reviewed.');

        } catch (\Exception $e) {
            $this->isUploading = false;
            $this->addError('upload', 'Failed to submit application: ' . $e->getMessage());

            // Log the error for debugging
            logger()->error('Vendor application upload failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'file_name' => $this->pdfFile?->getClientOriginalName()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.verification.vendor-application');
    }
}
