@extends('layouts.auth')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <img src="{{ asset('images/aktina-logo.svg') }}" alt="Aktina" class="h-12 mx-auto mb-4">
            <h1 class="text-3xl font-bold text-gray-900">Retailer Verification</h1>
            <p class="mt-2 text-gray-600">Complete your profile to access the Aktina platform</p>
        </div>

        <!-- Tab Navigation -->
        <div class="border-b border-gray-200 mb-6">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button
                    class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                    data-tab="demographics"
                    onclick="switchTab('demographics')"
                >
                    Demographics
                </button>
                <button
                    class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
                    data-tab="instructions"
                    onclick="switchTab('instructions')"
                >
                    Welcome & Instructions
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div id="demographics-tab" class="tab-content">
            @livewire('verification.retailer-demographics')
        </div>

        <div id="instructions-tab" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow border p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Welcome to Aktina SCM</h2>

                <div class="prose max-w-none">
                    <p class="text-gray-600 mb-4">
                        Welcome to the Aktina Supply Chain Management platform! As a retailer, you'll have access to powerful tools to manage your inventory, orders, and supplier relationships.
                    </p>

                    <h3 class="text-lg font-medium text-gray-900 mt-6 mb-3">Getting Started</h3>
                    <ul class="list-disc list-inside space-y-2 text-gray-600">
                        <li>Complete your demographics information in the Demographics tab</li>
                        <li>Once verified, you'll have access to the full platform</li>
                        <li>Explore your dashboard to see key metrics and alerts</li>
                        <li>Manage your product catalog and inventory levels</li>
                        <li>Place orders with approved suppliers and vendors</li>
                    </ul>

                    <h3 class="text-lg font-medium text-gray-900 mt-6 mb-3">Platform Features</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Inventory Management</h4>
                            <p class="text-sm text-gray-600">Track stock levels, set reorder points, and manage product information across all your locations.</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Order Processing</h4>
                            <p class="text-sm text-gray-600">Create and manage purchase orders with suppliers, track delivery status, and handle receipts.</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Supplier Network</h4>
                            <p class="text-sm text-gray-600">Connect with verified suppliers and vendors in the Aktina network for better sourcing opportunities.</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Analytics & Reporting</h4>
                            <p class="text-sm text-gray-600">Get insights into your supply chain performance with detailed reports and analytics.</p>
                        </div>
                    </div>

                    <h3 class="text-lg font-medium text-gray-900 mt-6 mb-3">Need Help?</h3>
                    <p class="text-gray-600">
                        If you need assistance, please contact our support team at <a href="mailto:support@aktina.com" class="text-blue-600 hover:text-blue-500">support@aktina.com</a> or call 1-800-AKTINA.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function switchTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });

    // Remove active classes from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });

    // Show selected tab content
    document.getElementById(tabName + '-tab').classList.remove('hidden');

    // Add active class to selected tab
    const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
    activeButton.classList.remove('border-transparent', 'text-gray-500');
    activeButton.classList.add('border-blue-500', 'text-blue-600');
}

// Initialize the first tab as active
document.addEventListener('DOMContentLoaded', function() {
    switchTab('demographics');
});
</script>
@endsection
