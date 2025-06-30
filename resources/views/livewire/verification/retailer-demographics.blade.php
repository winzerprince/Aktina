<div class="bg-white rounded-lg shadow border p-6">
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($isCompleted)
        <!-- Completed State -->
        <div class="text-center py-8">
            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                <div class="flex items-center justify-center mb-4">
                    <svg class="h-12 w-12 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-green-800 mb-2">Demographics Completed!</h3>
                <p class="text-green-700">Your retailer profile has been verified. You now have full access to the Aktina platform.</p>
                <div class="mt-4">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        Go to Dashboard
                    </a>
                </div>
            </div>
        </div>
    @else
        <!-- Demographics Form -->
        <div>
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Business Demographics</h2>

            <form wire:submit.prevent="submitDemographics">
                <!-- Business Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Business Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="business_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Business Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="business_name" wire:model="business_name"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Enter your business name">
                            @error('business_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="business_type" class="block text-sm font-medium text-gray-700 mb-1">
                                Business Type <span class="text-red-500">*</span>
                            </label>
                            <select id="business_type" wire:model="business_type"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select business type</option>
                                <option value="sole_proprietorship">Sole Proprietorship</option>
                                <option value="partnership">Partnership</option>
                                <option value="corporation">Corporation</option>
                                <option value="llc">LLC</option>
                                <option value="other">Other</option>
                            </select>
                            @error('business_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="business_registration_number" class="block text-sm font-medium text-gray-700 mb-1">
                                Registration Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="business_registration_number" wire:model="business_registration_number"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Business registration number">
                            @error('business_registration_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="tax_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Tax ID <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="tax_id" wire:model="tax_id"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Tax identification number">
                            @error('tax_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Contact Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-1">
                                Contact Person <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="contact_person" wire:model="contact_person"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Primary contact person">
                            @error('contact_person') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" id="phone" wire:model="phone"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Business phone number">
                            @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="website" class="block text-sm font-medium text-gray-700 mb-1">
                                Website
                            </label>
                            <input type="url" id="website" wire:model="website"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="https://www.example.com">
                            @error('website') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Business Address -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Business Address</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="street" class="block text-sm font-medium text-gray-700 mb-1">
                                Street Address <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="street" wire:model="business_address.street"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Street address">
                            @error('business_address.street') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">
                                City <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="city" wire:model="business_address.city"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="City">
                            @error('business_address.city') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700 mb-1">
                                State/Province <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="state" wire:model="business_address.state"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="State or province">
                            @error('business_address.state') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">
                                Postal Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="postal_code" wire:model="business_address.postal_code"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Postal/ZIP code">
                            @error('business_address.postal_code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 mb-1">
                                Country <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="country" wire:model="business_address.country"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Country">
                            @error('business_address.country') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Business Details -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Business Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="annual_revenue" class="block text-sm font-medium text-gray-700 mb-1">
                                Annual Revenue <span class="text-red-500">*</span>
                            </label>
                            <select id="annual_revenue" wire:model="annual_revenue"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select revenue range</option>
                                <option value="under_100k">Under $100K</option>
                                <option value="100k_500k">$100K - $500K</option>
                                <option value="500k_1m">$500K - $1M</option>
                                <option value="1m_5m">$1M - $5M</option>
                                <option value="5m_10m">$5M - $10M</option>
                                <option value="over_10m">Over $10M</option>
                            </select>
                            @error('annual_revenue') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="employee_count" class="block text-sm font-medium text-gray-700 mb-1">
                                Employee Count <span class="text-red-500">*</span>
                            </label>
                            <select id="employee_count" wire:model="employee_count"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select employee count</option>
                                <option value="1_5">1-5 employees</option>
                                <option value="6_25">6-25 employees</option>
                                <option value="26_100">26-100 employees</option>
                                <option value="101_500">101-500 employees</option>
                                <option value="over_500">Over 500 employees</option>
                            </select>
                            @error('employee_count') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="years_in_business" class="block text-sm font-medium text-gray-700 mb-1">
                                Years in Business <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="years_in_business" wire:model="years_in_business"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Years in business" min="0" max="200">
                            @error('years_in_business') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <div>
                            <label for="primary_products" class="block text-sm font-medium text-gray-700 mb-1">
                                Primary Products/Services <span class="text-red-500">*</span>
                            </label>
                            <textarea id="primary_products" wire:model="primary_products" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Describe your primary products or services"></textarea>
                            @error('primary_products') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="mt-4">
                            <label for="target_market" class="block text-sm font-medium text-gray-700 mb-1">
                                Target Market <span class="text-red-500">*</span>
                            </label>
                            <textarea id="target_market" wire:model="target_market" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Describe your target market and customer base"></textarea>
                            @error('target_market') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            wire:loading.attr="disabled"
                            wire:target="submitDemographics"
                            :disabled="$isSubmitting">
                        <span wire:loading.remove wire:target="submitDemographics">
                            Submit Demographics
                        </span>
                        <span wire:loading wire:target="submitDemographics" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Submitting...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('redirect-to-dashboard', () => {
            setTimeout(() => {
                window.location.href = '{{ route("dashboard") }}';
            }, 2000);
        });
    });
</script>
