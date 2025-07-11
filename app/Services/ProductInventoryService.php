<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\DB;
use Exception;

class ProductInventoryService
{
    public function __construct(
        private ProductRepository $productRepository
    ) {}

    /**
     * Get company quantity for a specific product
     */
    public function getCompanyQuantity(Product $product, string $companyName): int
    {
        return $product->getCompanyQuantity($companyName);
    }

    /**
     * Set company quantity for a product
     */
    public function setCompanyQuantity(Product $product, string $companyName, int $quantity): bool
    {
        try {
            DB::beginTransaction();

            $product->setCompanyQuantity($companyName, $quantity);
            $product->save();

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Failed to update company quantity: " . $e->getMessage());
        }
    }

    /**
     * Transfer quantity between companies
     */
    public function transferQuantity(Product $product, string $fromCompany, string $toCompany, int $quantity): bool
    {
        try {
            DB::beginTransaction();

            $fromQuantity = $product->getCompanyQuantity($fromCompany);

            if ($fromQuantity < $quantity) {
                throw new Exception("Insufficient quantity for {$fromCompany}");
            }

            $toQuantity = $product->getCompanyQuantity($toCompany);

            $product->setCompanyQuantity($fromCompany, $fromQuantity - $quantity);
            $product->setCompanyQuantity($toCompany, $toQuantity + $quantity);

            $product->save();

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Failed to transfer quantity: " . $e->getMessage());
        }
    }

    /**
     * Check if company has sufficient quantity
     */
    public function hasSufficientQuantity(Product $product, string $companyName, int $requiredQuantity): bool
    {
        return $product->getCompanyQuantity($companyName) >= $requiredQuantity;
    }

    /**
     * Get all companies with quantities for a product
     */
    public function getCompaniesWithQuantities(Product $product): array
    {
        return $product->company_quantities ?? [];
    }

    /**
     * Get total quantity across all companies for a product
     */
    public function getTotalQuantity(Product $product): int
    {
        return $product->total_quantity;
    }
}
