# Aktina Supply Chain Management System Context

Aktina SCM is a Laravel-based supply chain management system for managing the supply chain operations of Aktina Technologies. The system includes the following key features:

1. User management with different roles (admin, hr_manager, production_manager, supplier, vendor, retailer)
2. Product management
3. Order tracking and management (Major refactoring in progress - Phase 2 of 4)
4. Resource and BOM (Bill of Materials) management
5. Production planning and tracking
6. Vendor and supplier management
7. Retailer management and listings
8. **Communication system for secure messaging between roles (FULLY IMPLEMENTED ✅)**

The system follows Laravel best practices, including:
- Service-Repository pattern for code organization
- Eloquent ORM for database interactions
- Blade templates for views
- Livewire for dynamic UI components
- **Modern HTML + Tailwind CSS components** (replaced Mary UI)
- ApexCharts for data visualization
- Tailwind CSS for styling

## Latest Implementation: WhatsApp-like Communication System ✅

**Communication Feature - FULLY IMPLEMENTED (July 8, 2025)**

A complete WhatsApp-like chat/communication feature has been successfully implemented with:

**Core Features:**
- Real-time messaging between users based on role relationships
- File sharing capabilities (images, documents)
- Message status indicators (read/unread)
- Conversation list with last message previews
- Contact selection with role-based filtering
- Responsive design for desktop and mobile

**Technical Implementation:**
- Service-Repository pattern for clean architecture
- Livewire components for dynamic UI
- Role-based communication permissions
- Dark/Light mode support with proper contrast
- Anti-flickering optimizations
- File upload and download functionality

**Components Implemented:**
1. `ConversationList` - Shows user conversations with pagination
2. `ContactList` - Available contacts based on role permissions
3. `MessageThread` - Main chat interface with real-time updates
4. `CommunicationPermissionService` - Role-based access control

**Database Structure:**
- Conversations table (user_one_id, user_two_id)
- Messages table (conversation_id, sender_id, content, is_read)
- Message files table for attachments

**Recent Fixes:**
- Fixed text visibility issues in dark/light modes
- Resolved Livewire component flickering
- Added missing service methods (createMessage, addMessageFile, markMessagesAsRead)
- Improved send button states and loading indicators

## Recent UI/UX Improvements

**Admin Dashboard Enhancement - Phase 1 COMPLETE ✅**

**Phase 1: Core Admin Dashboard Components (100% Complete)**
1. **AdminDashboardOverview Component**: 
   - Comprehensive analytics with revenue, orders, users, and inventory metrics
   - Advanced caching with 300-second TTL for expensive queries
   - Export functionality for data portability
   - Real-time polling for dynamic updates
   - Responsive design with modern Tailwind CSS

2. **SystemMonitoring Component**:
   - Real-time system health monitoring (CPU, Memory, Disk, Load)
   - Advanced ApexCharts for performance visualization
   - System alerts and logs management
   - Auto-refresh every 30 seconds
   - Color-coded health indicators

3. **AdminAnalytics Component**:
   - Multi-metric analytics (revenue, orders, users, inventory)
   - Dynamic chart types (line, bar, area)
   - Comprehensive export capabilities (CSV, JSON)
   - Report generation functionality
   - Advanced filtering and date range selection

4. **UserManagement Component**:
   - Full CRUD operations for user management
   - Advanced search, filtering, and pagination
   - Bulk operations (activate, deactivate, delete)
   - Role-based management system
   - Export functionality and user statistics

5. **OrderManagement Component**:
   - Comprehensive order tracking and status management
   - Priority-based order processing
   - Bulk operations for order management
   - Advanced filtering and search capabilities
   - Detailed order modals with item breakdowns

## Major Refactoring Initiative: Product Ownership System (In Planning - July 2025)

**Product Ownership Refactoring - MAJOR ARCHITECTURAL CHANGE**

A comprehensive refactoring initiative to implement company-based product ownership and inventory tracking:

**Key Changes:**
- Product ownership will be tracked by company rather than individual users
- JSON-based company quantity tracking: `{"Aktina": {"quantity": 100, "updated_at": "2025-07-10"}}`
- Total product quantity will be calculated dynamically from company quantities
- Order displays will show company names primarily with individual names as secondary info
- Automatic inventory transfers between companies during order processing

**Impact Areas:**
- Database schema (products table, orders display logic)
- Product and Order models and relationships
- Order creation and processing workflows
- Livewire components for orders and product management
- Inventory management system
- Service and repository layers

**Business Logic:**
- Users buy/sell on behalf of their companies, not as individuals
- Company inventory tracking replaces individual ownership
- Automated inventory transfers maintain accurate company-wise stock levels
- Enhanced order displays show business relationships clearly

**Implementation Approach:**
- Phased implementation starting with database changes
- Focus on basic functionality before complex features
- Maintain backward compatibility during transition
- Comprehensive testing at each phase
