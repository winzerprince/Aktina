<?php

namespace App\Livewire\Verification;

use App\Interfaces\Services\VerificationServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
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

    protected function rules()
    {
        return [
            'business_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-\.\&\']+$/' // Allow alphanumeric, spaces, hyphens, dots, ampersands, apostrophes
            ],
            'business_type' => [
                'required',
                'string',
                Rule::in(['retail', 'wholesale', 'e-commerce', 'franchise', 'chain_store', 'department_store', 'specialty_store', 'other'])
            ],
            'business_registration_number' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9\-]+$/' // Allow alphanumeric and hyphens only
            ],
            'tax_id' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9\-]+$/' // Allow alphanumeric and hyphens only
            ],
            'contact_person' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s\-\.\']+$/' // Allow letters, spaces, hyphens, dots, apostrophes
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^[\+]?[0-9\s\-\(\)\.]+$/' // Allow phone number formats
            ],
            'website' => [
                'nullable',
                'url',
                'max:255',
                'regex:/^https?:\/\/([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/' // Basic URL validation
            ],
            'annual_revenue' => [
                'required',
                'string',
                Rule::in(['under_100k', '100k_500k', '500k_1m', '1m_5m', '5m_10m', '10m_50m', 'over_50m'])
            ],
            'employee_count' => [
                'required',
                'string',
                Rule::in(['1_5', '6_10', '11_25', '26_50', '51_100', '101_500', 'over_500'])
            ],
            'years_in_business' => [
                'required',
                'integer',
                'min:0',
                'max:200'
            ],
            'primary_products' => [
                'required',
                'string',
                'max:1000'
            ],
            'target_market' => [
                'required',
                'string',
                'max:1000'
            ],
            'business_address.street' => [
                'required',
                'string',
                'max:255'
            ],
            'business_address.city' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z\s\-\.\']+$/' // Allow letters, spaces, hyphens, dots, apostrophes
            ],
            'business_address.state' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z\s\-\.\']+$/' // Allow letters, spaces, hyphens, dots, apostrophes
            ],
            'business_address.postal_code' => [
                'required',
                'string',
                'max:20',
                'regex:/^[a-zA-Z0-9\s\-]+$/' // Allow alphanumeric, spaces, hyphens
            ],
            'business_address.country' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z\s\-\.\']+$/' // Allow letters, spaces, hyphens, dots, apostrophes
            ],
        ];
    }

    protected $messages = [
        'business_name.required' => 'Business name is required.',
        'business_name.regex' => 'Business name contains invalid characters.',
        'business_type.required' => 'Please select a business type.',
        'business_type.in' => 'Please select a valid business type.',
        'business_registration_number.required' => 'Business registration number is required.',
        'business_registration_number.regex' => 'Business registration number format is invalid.',
        'tax_id.required' => 'Tax ID is required.',
        'tax_id.regex' => 'Tax ID format is invalid.',
        'contact_person.required' => 'Contact person name is required.',
        'contact_person.regex' => 'Contact person name contains invalid characters.',
        'phone.required' => 'Phone number is required.',
        'phone.regex' => 'Phone number format is invalid.',
        'website.url' => 'Please enter a valid website URL.',
        'website.regex' => 'Website URL format is invalid.',
        'annual_revenue.required' => 'Please select annual revenue range.',
        'annual_revenue.in' => 'Please select a valid revenue range.',
        'employee_count.required' => 'Please select employee count range.',
        'employee_count.in' => 'Please select a valid employee count range.',
        'years_in_business.required' => 'Years in business is required.',
        'years_in_business.integer' => 'Years in business must be a number.',
        'years_in_business.min' => 'Years in business cannot be negative.',
        'years_in_business.max' => 'Years in business seems unrealistic.',
        'primary_products.required' => 'Primary products description is required.',
        'target_market.required' => 'Target market description is required.',
        'business_address.street.required' => 'Street address is required.',
        'business_address.city.required' => 'City is required.',
        'business_address.city.regex' => 'City name contains invalid characters.',
        'business_address.state.required' => 'State is required.',
        'business_address.state.regex' => 'State name contains invalid characters.',
        'business_address.postal_code.required' => 'Postal code is required.',
        'business_address.postal_code.regex' => 'Postal code format is invalid.',
        'business_address.country.required' => 'Country is required.',
        'business_address.country.regex' => 'Country name contains invalid characters.',
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
        // Authorization check
        if (!auth()->user()->hasRole('retailer') || auth()->user()->is_verified) {
            $this->addError('authorization', 'You are not authorized to submit demographics.');
            return;
        }

        // Rate limiting check
        if ($this->isSubmitting) {
            return; // Prevent double submission
        }

        $this->validate();

        try {
            $this->isSubmitting = true;

            $user = Auth::user();
            $retailer = $user->retailer;

            if (!$retailer) {
                $this->addError('general', 'Retailer profile not found. Please contact support.');
                return;
            }

            // Sanitize input data
            $data = [
                'business_name' => trim(strip_tags($this->business_name)),
                'business_type' => $this->business_type,
                'business_registration_number' => trim(strip_tags($this->business_registration_number)),
                'tax_id' => trim(strip_tags($this->tax_id)),
                'contact_person' => trim(strip_tags($this->contact_person)),
                'phone' => trim(strip_tags($this->phone)),
                'website' => $this->website ? trim($this->website) : null,
                'annual_revenue' => $this->annual_revenue,
                'employee_count' => $this->employee_count,
                'years_in_business' => (int) $this->years_in_business,
                'primary_products' => trim(strip_tags($this->primary_products)),
                'target_market' => trim(strip_tags($this->target_market)),
                'business_address' => [
                    'street' => trim(strip_tags($this->business_address['street'])),
                    'city' => trim(strip_tags($this->business_address['city'])),
                    'state' => trim(strip_tags($this->business_address['state'])),
                    'postal_code' => trim(strip_tags($this->business_address['postal_code'])),
                    'country' => trim(strip_tags($this->business_address['country'])),
                ],
                'demographics_completed' => true,
            ];

            $retailer->update($data);

            // Use verification service to handle completion and notifications
            $verificationService = app(VerificationServiceInterface::class);
            $verificationService->completeRetailerDemographics($user);

            session()->flash('success', 'Demographics information saved successfully! You now have full access to the platform.');

            $this->isCompleted = true;

            // Log successful submission
            logger()->info('Retailer demographics submitted successfully', [
                'user_id' => $user->id,
                'retailer_id' => $retailer->id
            ]);

            // Redirect to dashboard after a short delay
            $this->dispatch('redirect-to-dashboard');

        } catch (\Exception $e) {
            $this->addError('general', 'Failed to save demographics: ' . $e->getMessage());

            // Log the error
            logger()->error('Retailer demographics submission failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
        } finally {
            $this->isSubmitting = false;
        }
    }

    public function render()
    {
        return view('livewire.verification.retailer-demographics');
    }
}
