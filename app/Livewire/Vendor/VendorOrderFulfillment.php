<?php

namespace App\Livewire\Vendor;

use App\Models\Order;
use App\Interfaces\Services\VendorSalesServiceInterface;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class VendorOrderFulfillment extends Component
{
    public $order;
    public $orderId;
    public $step = 1;
    public $maxSteps = 3;
    public $currentStatus;
    public $nextStatus;
    public $trackingNumber = '';
    public $shippingProvider = '';
    public $fulfillmentNote = '';
    public $returnReason = '';
    public $partialFulfillmentDetails = '';
    public $errorMessage = '';
    public $successMessage = '';
    public $validNextStatuses = [];
    public $shippingProviders = [
        'ups' => 'UPS',
        'fedex' => 'FedEx',
        'usps' => 'USPS',
        'dhl' => 'DHL',
        'other' => 'Other'
    ];
    public $returnReasons = [
        'damaged' => 'Product Damaged',
        'wrong_item' => 'Wrong Item Sent',
        'customer_changed_mind' => 'Customer Changed Mind',
        'quality_issue' => 'Quality Issue',
        'other' => 'Other Reason'
    ];

    public function mount($orderId)
    {
        $this->orderId = $orderId;
        $this->loadOrder();
    }

    public function loadOrder()
    {
        $this->order = Order::where('id', $this->orderId)
            ->where('seller_id', auth()->id())
            ->first();

        if (!$this->order) {
            $this->errorMessage = 'Order not found or access denied';
            return;
        }

        $this->currentStatus = $this->order->status;
        $vendorSalesService = app(VendorSalesServiceInterface::class);
        $this->validNextStatuses = $vendorSalesService->getValidNextStatuses($this->order);

        // Default to the first valid next status
        $this->nextStatus = array_key_first($this->validNextStatuses);

        // Determine the max steps based on current status
        if (in_array($this->order->status, [Order::STATUS_SHIPPED, Order::STATUS_IN_TRANSIT])) {
            $this->maxSteps = 2; // Skip fulfillment step
        } else if ($this->order->status === Order::STATUS_DELIVERED) {
            $this->maxSteps = 1; // Only completion step
        }
    }

    public function nextStep()
    {
        if ($this->step < $this->maxSteps) {
            $this->validateCurrentStep();
            $this->step++;
        } else {
            $this->completeProcess();
        }
    }

    public function previousStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function validateCurrentStep()
    {
        $this->errorMessage = '';

        if ($this->step === 1) {
            if (empty($this->nextStatus)) {
                $this->errorMessage = 'Please select a valid status';
                return false;
            }
        }

        if ($this->step === 2) {
            // Validation based on the selected next status
            switch ($this->nextStatus) {
                case Order::STATUS_SHIPPED:
                    $this->validate([
                        'trackingNumber' => 'required|string|min:3',
                        'shippingProvider' => 'required|string',
                    ], [
                        'trackingNumber.required' => 'Tracking number is required for shipping',
                        'shippingProvider.required' => 'Please select a shipping provider',
                    ]);
                    break;

                case Order::STATUS_PARTIALLY_FULFILLED:
                    $this->validate([
                        'partialFulfillmentDetails' => 'required|string|min:10',
                    ], [
                        'partialFulfillmentDetails.required' => 'Please provide details about the partial fulfillment',
                    ]);
                    break;

                case Order::STATUS_RETURNED:
                    $this->validate([
                        'returnReason' => 'required|string',
                    ], [
                        'returnReason.required' => 'Please select a return reason',
                    ]);
                    break;

                case Order::STATUS_FULFILLMENT_FAILED:
                    $this->validate([
                        'fulfillmentNote' => 'required|string|min:5',
                    ], [
                        'fulfillmentNote.required' => 'Please provide a reason for the fulfillment failure',
                    ]);
                    break;
            }
        }

        return true;
    }

    public function completeProcess()
    {
        try {
            if (!$this->validateCurrentStep()) {
                return;
            }

            $vendorSalesService = app(VendorSalesServiceInterface::class);
            $additionalData = [];

            // Prepare additional data based on the status
            switch ($this->nextStatus) {
                case Order::STATUS_SHIPPED:
                    $additionalData = [
                        'tracking_number' => $this->trackingNumber,
                        'shipping_provider' => $this->shippingProvider,
                    ];
                    break;

                case Order::STATUS_PARTIALLY_FULFILLED:
                    $additionalData = [
                        'partial_details' => $this->partialFulfillmentDetails,
                    ];
                    break;

                case Order::STATUS_RETURNED:
                    $additionalData = [
                        'return_reason' => $this->returnReason,
                    ];
                    break;

                case Order::STATUS_FULFILLMENT_FAILED:
                    $additionalData = [
                        'failure_reason' => $this->fulfillmentNote,
                    ];
                    break;
            }

            $success = $vendorSalesService->processOrderStatusUpdate(
                $this->orderId,
                $this->nextStatus,
                auth()->id(),
                $additionalData
            );

            if ($success) {
                $this->successMessage = 'Order status updated successfully to ' . $this->getStatusLabel($this->nextStatus);
                $this->dispatch('order-updated');
                $this->loadOrder(); // Refresh order data
            }
        } catch (ValidationException $e) {
            $this->errorMessage = $e->getMessage();
        } catch (\Exception $e) {
            $this->errorMessage = 'An error occurred while processing your request. Please try again.';
            \Log::error('Vendor order fulfillment error', ['message' => $e->getMessage()]);
        }
    }

    public function getStatusLabel($status)
    {
        $statusLabels = [
            Order::STATUS_PENDING => 'Pending',
            Order::STATUS_ACCEPTED => 'Accepted',
            Order::STATUS_REJECTED => 'Rejected',
            Order::STATUS_PROCESSING => 'Processing',
            Order::STATUS_PARTIALLY_FULFILLED => 'Partially Fulfilled',
            Order::STATUS_FULFILLED => 'Fulfilled',
            Order::STATUS_SHIPPED => 'Shipped',
            Order::STATUS_IN_TRANSIT => 'In Transit',
            Order::STATUS_DELIVERED => 'Delivered',
            Order::STATUS_COMPLETE => 'Complete',
            Order::STATUS_CANCELLED => 'Cancelled',
            Order::STATUS_RETURNED => 'Returned',
            Order::STATUS_FULFILLMENT_FAILED => 'Fulfillment Failed',
        ];

        return $statusLabels[$status] ?? ucfirst($status);
    }

    public function render()
    {
        return view('livewire.vendor.vendor-order-fulfillment');
    }
}
