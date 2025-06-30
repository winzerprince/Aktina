# Order Business Logic Implementation Plan

This plan outlines the steps to implement order business logic for Aktina, including order repository, service, and the necessary components to support the required functionality.

## Phase 1: Database Modifications

### Step 1: Create Employees Table
- Create migration for employees table with:
  - `id` - Primary key
  - `name` - Employee name
  - `role` - Employee role/position
  - `status` - Available/unavailable
  - `current_activity` - Managing order/Managing production
  - `order_id` - Nullable FK to orders
  - `production_id` - Nullable FK to productions
  - Timestamps
- Create Employee model with relations to Order and Production
- Create employee factory and seeder

### Step 2: Create Resource Orders Table
- Create migration for resource_orders table with:
  - `id` - Primary key
  - `price` - Total price
  - `items` - JSON field for resource IDs and quantities
  - `status` - enum (pending, accepted, complete)
  - `buyer_id` - FK to users (Aktina as buyer)
  - `seller_id` - FK to users (Supplier as seller)
  - Timestamps
- Create ResourceOrder model with relations to User and Resource

### Step 3: Update Product Factory and Seeders
- Modify product factory to use specific products list (Aktina 26 pro, Sakina 26 mini, etc.)
- Update product seeder to ensure consistent categories and product numbers

### Step 4: Update Supplier Factory and Seeders
- Modify supplier factory to create 6 specific suppliers for different components
- Update supplier seeder to ensure they provide specific raw materials

## Phase 2: Repositories and Services Implementation

### Step 5: Create Order Repository(keep in mind how the owner of the product changes(owner_id) for each operation)
- Create OrderRepositoryInterface with methods for:
  - Getting all orders
  - Getting order by id
  - Creating new order
  - Updating order status
  - Getting orders by buyer/seller
  - Getting orders by status
  - Checking product stock levels
  - Assigning employees to order
- Implement OrderRepository class

### Step 6: Create ResourceOrder Repository
- Create ResourceOrderRepositoryInterface with methods for:
  - Getting all resource orders
  - Getting resource order by id
  - Creating new resource order
  - Updating resource order status
  - Getting resource orders by buyer/seller
  - Checking resource stock levels
- Implement ResourceOrderRepository class

### Step 7: Create Order Service(keep in mind options for choosing order by start and end date, end date should use carbon endofday function)
- Create OrderServiceInterface with methods for:
  - Processing new orders
  - Getting order details
  - Accepting orders
  - Completing orders
  - Checking stock availability
  - Handling inventory adjustments
  - Selecting employees for order fulfillment
  - Sending order notifications
  - creating order report
- Implement OrderService class


### Step 8: Create ResourceOrder Service
- Create ResourceOrderServiceInterface with methods for:
  - Processing new resource orders
  - Getting resource order details
  - Accepting resource orders
  - Completing resource orders
  - Handling resource inventory adjustments
- Implement ResourceOrderService class

## Phase 3: Livewire Components

### Step 9: Create Order Creation Component
- Create Livewire component for order form
- Implement logic for:
  - Product selection with quantities
  - Maximum quantity validation based on reorder level
  - Default supplier selection
  - Order submission

### Step 10: Create Orders List Component
- Create Livewire component to display orders
- Implement filters for status and date
- Implement sorting functionality

### Step 11: Create Order Detail Component
- Create component to view order details
- Implement warnings for low stock items
- Add button for production redirection
- Implement accept functionality with employee assignment
- Implement received/complete functionality

### Step 12: Create ResourceOrder Components
- Create similar components for resource orders
- Adapt functionality for resource-specific logic

## Phase 4: Notifications and Inventory Management

### Step 13: Implement Laravel Notifications
- Create OrderCreatedNotification class
- Create OrderAcceptedNotification class
- Create OrderCompletedNotification class
- Create LowStockNotification class
- Set up notification channels (database, mail)

### Step 14: Implement Inventory Management Functionality
- Create methods to update product inventory on order acceptance
- Implement warning system for low stock
- Create functionality to trigger production for low stock items

## Phase 5: Routes, Controllers and Views

### Step 15: Create Order Controller
- Create controller methods for order operations
- Integrate with order service

### Step 16: Create ResourceOrder Controller
- Create controller methods for resource order operations
- Integrate with resource order service

### Step 17: Create Routes
- Set up routes for order operations
- Set up routes for resource order operations

### Step 18: Create Blade Views
- Create order form view
- Create orders list view
- Create order detail view
- Create resource orders views

## Phase 6: Testing and Finalization

### Step 19: Create Unit Tests
- Test order repository
- Test resource order repository
- Test order service
- Test resource order service
- Test employee selection logic

### Step 20: Create Feature Tests
- Test order creation flow
- Test order acceptance flow
- Test order completion flow
- Test resource order flows

### Step 21: Documentation
- Document new services and repositories
- Update context.md with new features
- Document order flow and business rules
