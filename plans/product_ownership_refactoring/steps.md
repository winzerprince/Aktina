# Product Ownership Refactoring Plan

## Overview
Refactor the product ownership model to track company-wise quantities and update order displays to show company names instead of individual buyer names.

## Requirements Summary
- Add JSON field to products table for company-wise quantity tracking
- Calculate total quantity dynamically from JSON field
- Use detailed JSON structure: `{"Aktina": {"quantity": 100, "updated_at": "2025-07-10"}, "TechCorp": {"quantity": 50, "updated_at": "2025-07-09"}}`
- Show company name primarily with individual name as secondary info in orders
- Automatically deduct from seller's company and add to buyer's company when orders are processed
- Focus on basic functionality first, no complex implementations

## Phase 1: Database Schema Changes
### Step 1.1: Create Product Ownership Migration
- Add `company_quantities` JSON field to products table
- Remove or modify existing `owner_id` field if needed
- Add indexes for performance

### Step 1.2: Update Product Model
- Add `company_quantities` to fillable fields
- Add JSON casting for the field
- Create helper methods for quantity management
- Add method to calculate total quantity dynamically

### Step 1.3: Create Database Seeder Updates
- Update existing product seeders to include company quantities
- Ensure data consistency with existing products

## Phase 2: Core Service Layer
### Step 2.1: Create Product Inventory Service
- Create service for managing company-wise product quantities
- Implement methods for adding/removing quantities by company
- Add validation for quantity operations
- Include automatic total quantity calculation

### Step 2.2: Create Order Processing Service
- Create service for handling order fulfillment
- Implement automatic inventory transfer between companies
- Add validation for sufficient inventory
- Include rollback mechanisms for failed orders

### Step 2.3: Update Repository Layer
- Update ProductRepository with new methods
- Add company-specific inventory queries
- Optimize database queries for performance

## Phase 3: Order System Refactoring
### Step 3.1: Update Order Model and Migration
- Add company tracking fields if needed
- Update order relationships
- Modify order display logic

### Step 3.2: Update Order Creation Logic
- Modify order creation to use company names
- Update validation rules
- Implement inventory checking against company quantities

### Step 3.3: Update Order Display Views
- Modify order lists to show company names primarily
- Update order details to show company info
- Adjust order creation forms

## Phase 4: Livewire Component Updates
### Step 4.1: Update Order Creation Component
- Modify buyer/seller selection to emphasize company names
- Update display logic for company + individual names
- Implement real-time inventory checking

### Step 4.2: Update Order Management Components
- Modify order lists and displays
- Update filtering and searching by company
- Adjust order status displays

### Step 4.3: Update Product Management Components
- Create company inventory display components
- Add inventory transfer functionality
- Implement inventory tracking views

## Phase 5: Testing and Validation
### Step 5.1: Create Unit Tests
- Test product inventory service methods
- Test order processing service
- Test model methods and relationships

### Step 5.2: Create Feature Tests
- Test order creation workflow
- Test inventory transfer scenarios
- Test edge cases and error handling

### Step 5.3: Manual Testing
- Test UI components
- Verify data consistency
- Check performance impact

## Phase 6: Final Integration
### Step 6.1: Update Existing Data
- Create migration script for existing products
- Convert existing orders to new format
- Verify data integrity

### Step 6.2: Update Documentation
- Update API documentation
- Update user guides
- Document new business logic

### Step 6.3: Performance Optimization
- Optimize database queries
- Add caching where appropriate
- Monitor performance metrics

## Success Criteria
- Products have company-wise quantity tracking
- Orders display company names prominently
- Inventory automatically transfers between companies
- All existing functionality remains intact
- No data loss during migration
- Performance remains acceptable
