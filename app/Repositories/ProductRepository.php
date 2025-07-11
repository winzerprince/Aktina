<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository
{
    /**
     * Find product by ID
     */
    public function findById(int $id): ?Product
    {
        return Product::find($id);
    }

    /**
     * Get all products
     */
    public function getAll(): Collection
    {
        return Product::all();
    }

    /**
     * Find products by company with quantities
     */
    public function getProductsByCompany(string $companyName): Collection
    {
        return Product::whereJsonContains('company_quantities->' . $companyName, fn($query) => $query)
                     ->get();
    }

    /**
     * Find products with low stock for a company
     */
    public function getLowStockByCompany(string $companyName, int $threshold = 10): Collection
    {
        return Product::whereRaw(
            "JSON_EXTRACT(company_quantities, '$.\"$companyName\".quantity') < ?",
            [$threshold]
        )->get();
    }

    /**
     * Search products by name or model
     */
    public function search(string $term): Collection
    {
        return Product::where('name', 'like', "%{$term}%")
                     ->orWhere('model', 'like', "%{$term}%")
                     ->orWhere('sku', 'like', "%{$term}%")
                     ->get();
    }

    /**
     * Update product
     */
    public function update(Product $product, array $data): bool
    {
        return $product->update($data);
    }

    /**
     * Create product
     */
    public function create(array $data): Product
    {
        return Product::create($data);
    }
}
