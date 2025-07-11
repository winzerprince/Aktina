# Seeder & Factory Refactoring Plan

## Overview
Refactor seeders and factories to create a logical company-based data structure for the Aktina SCM system with proper supply chain relationships.

## Requirements Analysis
1. **6 Products Total**: 3 existing + 3 new Aktina products
2. **Company Structure**:
   - Aktina (manufacturer): admin, hr_manager, production_manager
   - Vendor Company: vendor  
   - Retailer Company: retailer
   - Supplier Company: supplier
3. **Product Quantities**: Aktina (highest) → Vendor (medium) → Retailer (lowest), Supplier (none)
4. **Realistic Supply Chain Orders**: Following business logic

## Phase 1: User Seeder Updates
### Step 1.1: Fix Test User Company Assignments
- Ensure admin@gmail.com, hr_manager@gmail.com, production_manager@gmail.com → "Aktina"
- Ensure vendor@gmail.com → "Vendor Company"
- Ensure retailer@gmail.com → "Retailer Company"  
- Ensure supplier@gmail.com → "Supplier Company"

### Step 1.2: Update UserFactory for Realistic Company Names
- Admin users → Always "Aktina"
- Vendor users → Various vendor company names
- Retailer users → Various retailer company names
- Supplier users → Various supplier company names

## Phase 2: Product Seeder Updates
### Step 2.1: Create 6 Total Products
- Keep existing 3: Pro 15, Lite 12, Essential 10
- Add 3 new: Tab 10 (tablet), Watch 3 (smartwatch), Buds Pro (earbuds)

### Step 2.2: Set Logical Product Quantities
- Aktina: Highest quantities (manufacturer)
- Vendor Company: Medium quantities (reseller)
- Retailer Company: Lowest quantities (end seller)
- Supplier Company: No product quantities (supplies resources only)

## Phase 3: Order Seeder Updates
### Step 3.1: Create Realistic Supply Chain Orders
- Suppliers selling resources to Aktina
- Aktina selling products to vendors
- Vendors selling products to retailers
- Some direct Aktina to retailer orders

### Step 3.2: Maintain Test User Specific Orders
- Keep existing vendor@gmail.com orders as seller
- Add realistic buyer patterns

## Phase 4: Factory Updates
### Step 4.1: Update UserFactory
- Add realistic company name generation by role
- Ensure proper address and contact information

### Step 4.2: Update ProductFactory (if needed)
- Ensure consistent with new product structure

## Phase 5: Testing & Validation
### Step 5.1: Run Database Seeders
- Test all seeders execute without errors
- Verify data consistency

### Step 5.2: Validate Business Logic
- Check product quantity distributions
- Verify order relationships make sense
- Test user company assignments

## Files to Modify
1. `database/seeders/UserSeeder.php`
2. `database/seeders/ProductSeeder.php`
3. `database/seeders/OrderSeeder.php`
4. `database/factories/UserFactory.php`
5. `database/factories/ProductFactory.php` (if needed)

## Success Criteria
- ✅ 6 products with logical quantity distribution
- ✅ Test users have correct company assignments
- ✅ Random users have realistic company names
- ✅ Orders follow supply chain logic
- ✅ All seeders run without errors
- ✅ Data is consistent and logical
