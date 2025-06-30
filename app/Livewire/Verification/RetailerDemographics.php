<?php

namespace App\Livewire\Verification;

use App\Interfaces\Services\VerificationServiceInterface;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RetailerDemographics extends Component
{
    public $business_name = '';
    public $business_type = '';
    public $business_registration_number = '';
    public $tax_id = '';
    public $contact_person = '';
    public $phone = '';
    public $website = '';
    public $annual_revenue = '';
    public $employee_count = '';
    public $years_in_business = '';
    public $primary_products = '';
    public $target_market = '';
    public $business_address = [
        'street' => '',
        'city' => '',
        'state' => '',
        'postal_code' => '',
        'country' => 'United States'
    ];
    public $isSubmitting = false;
    public $isCompleted = false;

    protected $rules = [
        'business_name' => 'required|string|max:255',
        'business_type' => 'required|string',
        'business_registration_number' => 'required|string|max:100',
        'tax_id' => 'required|string|max:50',
        'contact_person' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'website' => 'nullable|url|max:255',
        'annual_revenue' => 'required|string',
        'employee_count' => 'required|string',
        'years_in_business' => 'required|integer|min:0|max:200',
        'primary_products' => 'required|string|max:1000',
        'target_market' => 'required|string|max:1000',
        'business_address.street' => 'required|string|max:255',
        'business_address.city' => 'required|string|max:100',
        'business_address.state' => 'required|string|max:100',
        'business_address.postal_code' => 'required|string|max:20',
        'business_address.country' => 'required|string|max:100',
    ];

    public function mount()
    {
        $this->loadExistingData();
    }

    public function loadExistingData()
    {
        $user = Auth::user();
        $retailer = $user->retailer;

        if ($retailer && $retailer->demographics_completed) {
            $this->isCompleted = true;
            $this->business_name = $retailer->business_name ?? '';
            $this->business_type = $retailer->business_type ?? '';
            $this->business_registration_number = $retailer->business_registration_number ?? '';
            $this->tax_id = $retailer->tax_id ?? '';
            $this->contact_person = $retailer->contact_person ?? '';
            $this->phone = $retailer->phone ?? '';
            $this->website = $retailer->website ?? '';
            $this->annual_revenue = $retailer->annual_revenue ?? '';
            $this->employee_count = $retailer->employee_count ?? '';
            $this->years_in_business = $retailer->years_in_business ?? '';
            $this->primary_products = $retailer->primary_products ?? '';
            $this->target_market = $retailer->target_market ?? '';
            $this->business_address = $retailer->business_address ?? $this->business_address;
        }
    }

    public function submitDemographics()
    {
        $this->validate();

        try {
            $this->isSubmitting = true;

            $user = Auth::user();
            $retailer = $user->retailer;

            if (!$retailer) {
                $this->addError('general', 'Retailer profile not found. Please contact support.');
                return;
            }

            $retailer->update([
                'business_name' => $this->business_name,
                'business_type' => $this->business_type,
                'business_registration_number' => $this->business_registration_number,
                'tax_id' => $this->tax_id,
                'contact_person' => $this->contact_person,
                'phone' => $this->phone,
                'website' => $this->website,
                'annual_revenue' => $this->annual_revenue,
                'employee_count' => $this->employee_count,
                'years_in_business' => $this->years_in_business,
                'primary_products' => $this->primary_products,
                'target_market' => $this->target_market,
                'business_address' => $this->business_address,
                'demographics_completed' => true,
            ]);

            // Use verification service to handle completion and notifications
            $verificationService = app(VerificationServiceInterface::class);
            $verificationService->completeRetailerDemographics($user);

            session()->flash('success', 'Demographics information saved successfully! You now have full access to the platform.');

            $this->isCompleted = true;

            // Redirect to dashboard after a short delay
            $this->dispatch('redirect-to-dashboard');

        } catch (\Exception $e) {
            $this->addError('general', 'Failed to save demographics: ' . $e->getMessage());
        } finally {
            $this->isSubmitting = false;
        }
    }

    public function render()
    {
        return view('livewire.verification.retailer-demographics');
    }
}
