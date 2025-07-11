<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderProcessingService
{
    public function __construct(
        private ProductInventoryService $inventoryService
    ) {}

    /**
     * Process order and transfer inventory between companies
     */
    public function processOrder(Order $order): bool
    {
        try {
            DB::beginTransaction();

            $buyerCompany = $order->buyer->company_name;
            $sellerCompany = $order->seller->company_name;

            foreach ($order->items as $item) {
                $product = Product::find($item['product_id']);
                $quantity = $item['quantity'];

                if (!$product) {
                    throw new Exception("Product not found: {$item['product_id']}");
                }

                // Check if seller has sufficient quantity
                if (!$this->inventoryService->hasSufficientQuantity($product, $sellerCompany, $quantity)) {
                    throw new Exception("Insufficient quantity for {$product->name} in {$sellerCompany}");
                }

                // Transfer quantity from seller to buyer
                $this->inventoryService->transferQuantity(
                    $product,
                    $sellerCompany,
                    $buyerCompany,
                    $quantity
                );
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Order processing failed: " . $e->getMessage());
        }
    }

    /**
     * Validate order before processing
     */
    public function validateOrder(Order $order): array
    {
        $errors = [];
        $sellerCompany = $order->seller->company_name;

        foreach ($order->items as $item) {
            $product = Product::find($item['product_id']);
            $quantity = $item['quantity'];

            if (!$product) {
                $errors[] = "Product not found: {$item['product_id']}";
                continue;
            }

            if (!$this->inventoryService->hasSufficientQuantity($product, $sellerCompany, $quantity)) {
                $available = $this->inventoryService->getCompanyQuantity($product, $sellerCompany);
                $errors[] = "Insufficient quantity for {$product->name}. Available: {$available}, Required: {$quantity}";
            }
        }

        return $errors;
    }

    /**
     * Get company name from user
     */
    private function getCompanyName(User $user): string
    {
        return $user->company_name ?? 'Unknown Company';
    }

    /**
     * Rollback order inventory changes
     */
    public function rollbackOrder(Order $order): bool
    {
        try {
            DB::beginTransaction();

            $buyerCompany = $order->buyer->company_name;
            $sellerCompany = $order->seller->company_name;

            foreach ($order->items as $item) {
                $product = Product::find($item['product_id']);
                $quantity = $item['quantity'];

                if ($product) {
                    // Transfer quantity back from buyer to seller
                    $this->inventoryService->transferQuantity(
                        $product,
                        $buyerCompany,
                        $sellerCompany,
                        $quantity
                    );
                }
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Order rollback failed: " . $e->getMessage());
        }
    }
}
