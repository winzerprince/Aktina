# Database Changes Plan

## Overview
This plan outlines all the changes needed to convert the Laravel application to follow proper naming conventions and update relationships.

## Changes Required

### 1. Table Naming Convention Changes
**Current → Target:**
- `product` → `products`
- `bom` → `boms`
- `resource` → `resources`
- `supplier` → `suppliers`
- `vendor` → `vendors`
- `admin` → `admins`
- `hr_manager` → `hr_managers`
- `production_manager` → `production_managers`
- `retailer` → `retailers`
- `retailer_listing` → `retailer_listings`
- `application` → `applications`
- `production` → `productions`
- `order` → `orders`
- `rating` → `ratings`

### 2. Relationship Changes

#### A. Products ↔ BOMs (One-to-One)
- **Current**: BOM belongs to Product (`bom.product_id`)
- **Target**: Product has one BOM (`products.bom_id` or keep current structure)
- **Action**: Ensure unique constraint on relationship

#### B. Resources ↔ BOMs (Many-to-Many)
- **Current**: Resource belongs to BOM (`resource.bom_id`)
- **Target**: Many-to-many relationship via pivot table
- **Action**: 
  - Remove `bom_id` from `resources` table
  - Create `bom_resource` pivot table

#### C. Retailer-Vendor Relationship Removal
- **Current**: Retailer belongs to Vendor (`retailer.vendor_id`)
- **Target**: Remove direct relationship
- **Action**: Drop `vendor_id` from `retailers` table

#### D. Application-Retailer Listing Relationship
- **Current**: RetailerListing has many Vendors (incorrect)
- **Target**: Application has many RetailerListings
- **Action**: Add `application_id` to `retailer_listings` table

## Step-by-Step Execution Plan

### Step 1: Product Table & Related Files
1. Edit migration: `2025_06_20_124448_create_product_table.php` → `create_products_table.php`
2. Update migration content to use `products` table
3. Update Product model to use `products` table
4. Update ProductFactory
5. Update ProductSeeder

### Step 2: BOM Table & Related Files
1. Edit migration: `2025_06_20_130641_create_bom_table.php` → `create_boms_table.php`
2. Update migration content to use `boms` table
3. Update Bom model to use `boms` table and new relationships
4. Update BomFactory
5. Update BomSeeder

### Step 3: Resource Table & Related Files
1. Edit migration: `2025_06_20_123237_create_resource_table.php` → `create_resources_table.php`
2. Update migration content to use `resources` table and remove `bom_id`
3. Update Resource model to use `resources` table and new relationships
4. Update ResourceFactory
5. Update ResourceSeeder

### Step 4: Create BOM-Resource Pivot Table
1. Create new migration for `bom_resource` pivot table
2. No model needed (Laravel handles pivot tables automatically)
3. Update seeders to populate pivot table

### Step 5: Supplier Table & Related Files
1. Edit migration: `2025_06_20_141437_create_supplier_table.php` → `create_suppliers_table.php`
2. Update migration content to use `suppliers` table
3. Update Supplier model
4. Update SupplierFactory
5. Update SupplierSeeder

### Step 6: Vendor Table & Related Files
1. Edit migration: `2025_06_20_142022_create_vendor_table.php` → `create_vendors_table.php`
2. Update migration content to use `vendors` table
3. Update Vendor model
4. Update VendorFactory
5. Update VendorSeeder

### Step 7: Application Table & Related Files
1. Edit migration: `2025_06_21_040727_create_application_table.php` → `create_applications_table.php`
2. Update migration content to use `applications` table
3. Update Application model
4. Update ApplicationFactory
5. Update ApplicationSeeder

### Step 8: Retailer Table & Related Files
1. Edit migration: `2025_06_20_143020_create_retailer_table.php` → `create_retailers_table.php`
2. Update migration content to use `retailers` table and remove `vendor_id`
3. Update Retailer model
4. Update RetailerFactory
5. Update RetailerSeeder

### Step 9: Retailer Listing Table & Related Files
1. Edit migration: `2025_06_20_145637_create_retailer_listing_table.php` → `create_retailer_listings_table.php`
2. Update migration content to use `retailer_listings` table and add `application_id`
3. Update RetailerListing model
4. Update RetailerListingFactory
5. Update RetailerListingSeeder

### Step 10: Admin Table & Related Files
1. Edit migration: `2025_06_20_141927_create_admin_table.php` → `create_admins_table.php`
2. Update migration content to use `admins` table
3. Update Admin model
4. Update AdminFactory
5. Update AdminSeeder

### Step 11: HR Manager Table & Related Files
1. Edit migration: `2025_06_20_142006_create_hr_manager_table.php` → `create_hr_managers_table.php`
2. Update migration content to use `hr_managers` table
3. Update HrManager model
4. Update HrManagerFactory
5. Update HrManagerSeeder

### Step 12: Production Manager Table & Related Files
1. Edit migration: `2025_06_20_141857_create_production_manager_table.php` → `create_production_managers_table.php`
2. Update migration content to use `production_managers` table
3. Update ProductionManager model
4. Update ProductionManagerFactory
5. Update ProductionManagerSeeder

### Step 13: Production Table & Related Files
1. Edit migration: `2025_06_20_125355_create_production_table.php` → `create_productions_table.php`
2. Update migration content to use `productions` table
3. Update Production model
4. Update ProductionFactory
5. Update ProductionSeeder

### Step 14: Order Table & Related Files
1. Edit migration: `2025_06_21_041122_create_order_table.php` → `create_orders_table.php`
2. Update migration content to use `orders` table
3. Update Order model
4. Update OrderFactory
5. Update OrderSeeder

### Step 15: Rating Table & Related Files
1. Edit migration: `2025_06_21_040530_create_rating_table.php` → `create_ratings_table.php`
2. Update migration content to use `ratings` table
3. Update Rating model
4. Update RatingFactory
5. Update RatingSeeder

### Step 16: Update Foreign Key References
1. Update the foreign key migration: `2025_06_23_082315_create_foreign_keys_production.php`
2. Update all foreign key references to use plural table names

## Notes
- Each step will be completed fully before moving to the next
- All relationships will be tested after changes
- Seeders will be updated to reflect new relationship structures
- Factory relationships will be updated accordingly
