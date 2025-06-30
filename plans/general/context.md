# Aktina Supply Chain Management System Context

Aktina SCM is a Laravel-based supply chain management system for managing the supply chain operations of Aktina Technologies. The system includes the following key features:

1. User management with different roles (admin, hr_manager, production_manager, supplier, vendor, retailer)
2. Product management
3. Order tracking and management
4. Resource and BOM (Bill of Materials) management
5. Production planning and tracking
6. Vendor and supplier management
7. Retailer management and listings

The system follows Laravel best practices, including:
- Service-Repository pattern for code organization
- Eloquent ORM for database interactions
- Blade templates for views
- Livewire for dynamic UI components
- **Modern HTML + Tailwind CSS components** (replaced Mary UI)
- ApexCharts for data visualization
- Tailwind CSS for styling

## Recent UI/UX Improvements (Latest Update)

**Mary UI Replacement Completed:**
1. **Removed all Mary UI dependencies** from Livewire components
2. **Fixed all "Trait not found" errors** by removing `use Toast;` statements
3. **Replaced with modern HTML + Tailwind CSS components** featuring:
   - Gradient stat cards with hover effects
   - Modern table designs with hover states
   - Clean button components with loading states
   - Status badges with consistent color schemes
   - Responsive card layouts with proper spacing
   - Modern timeline components for order tracking
   - Clean form controls and inputs

4. **Created new modern component library:**
   - `modern-dashboard.blade.php` - Complete dashboard layout
   - `modern-button.blade.php` - Versatile button component
   - `modern-card.blade.php` - Flexible card component
   - `modern-badge.blade.php` - Status badges
   - `modern-table.blade.php` - Responsive tables
   - `modern-input.blade.php` - Form inputs with validation
   - `modern-modal.blade.php` - Accessible modals

5. **Design improvements:**
   - Consistent color scheme (blue, amber, indigo, emerald, red)
   - Modern gradients and shadows
   - Smooth transitions and hover effects
   - Better responsive design
   - Improved accessibility

## Role-Based Pre-Verification System (Latest Major Feature)

**Comprehensive verification system implemented with:**

### Database & Models:
- Updated `applications` table with score, meeting_notes, and status fields
- Added `notifications` table for email and in-app notifications
- Enhanced `retailers` table with demographics fields
- Updated models for new relationships and functionality

### Service Layer:
- `VerificationService` for role verification logic
- `ApplicationService` for vendor application workflow
- Service-Repository pattern implementation with interfaces
- Automatic notifications at all workflow stages

### Pre-Verification Views:
- **Vendor Application System:** PDF upload, status tracking, progress indicators
- **Retailer Demographics:** Comprehensive form for business information
- **Basic Role Instructions:** Simple guidance for other roles
- **Real-time Notifications:** Both email and in-app notification system

### Admin Interface:
- **Application Management:** Complete CRUD operations for vendor applications
- **Verification Dashboard:** Stats overview and recent applications
- **PDF Viewer & Scoring:** Integrated application review workflow
- **Meeting Scheduler:** Meeting management for qualified applications

### Modern UI Components:
- **FileUpload Component:** Drag-and-drop with progress indicators
- **StatusBadge Component:** Consistent status visualization
- **ProgressIndicator Component:** Multi-step workflow visualization
- **Responsive Design:** Mobile-friendly layouts across all views

### Notification System:
- **Email Notifications:** 7 notification types for all workflow events
- **In-App Notifications:** Real-time updates with notification bell
- **Notification Management:** Mark as read, list view, dropdown view

### Security & Middleware:
- `EnsureRoleVerified` middleware for access control
- Role-based routing and permission checks
- File upload validation and security

**Status:** Fully implemented and integrated. Ready for Phase 7 testing and validation.

The database schema currently includes separate tables for each role with foreign keys to the users table, as well as tables for products, orders, resources, BOMs, productions, applications, and ratings.

Recent database refactoring improvements include:
1. Added status field to Orders (pending, accepted, complete)
2. Added owner_id to Products to track ownership changes
3. Added demographic fields to Retailers (male-female ratio, city, urban-rural classification, etc.)
4. Created a new Reports table for system-generated reports
5. Enhanced the Application table for PDF processing with Java server integration
6. Standardized all role names using snake_case (admin, hr_manager, production_manager, supplier, vendor, retailer)
7. Moved foreign keys from separate migrations into their respective table migrations
8. Updated controllers, routes, views, factories, and seeders to reflect the new schema and naming conventions
9. Restructured controller namespaces to follow snake_case for consistency

## Planned Feature: Pre-Verification Views System

**Major upcoming enhancement:** Role-based verification system where unverified users see specific onboarding views before accessing main application features:

**Verification Requirements by Role:**
- **Admin**: No verification needed - bypasses all requirements
- **Vendor**: PDF application upload → Java server scoring → Admin review → Meeting scheduling → Final approval
- **Retailer**: Mandatory demographics form completion with all fields required
- **Supplier/Production Manager/HR Manager**: Simple email verification with welcome instructions

**Key Components:**
1. **Database Updates**: Applications table enhancement with scoring and meeting fields
2. **Middleware System**: Role-based verification checks with email_verified_at dependency
3. **Pre-Verification Views**: Custom onboarding interfaces per role type
4. **Admin Interface**: Application management dashboard with approval workflow
5. **Notification System**: Email notifications for verification status changes
6. **Audit Trail**: Complete workflow tracking for vendor applications

**Technical Implementation:**
- Service-Repository pattern for verification logic
- Livewire components for dynamic UI
- HTML + Tailwind CSS for styling
- File upload system for PDF applications
- Integration with existing Java server for scoring

## Pre-Verification System Implementation (Latest Update - June 30, 2025)

**Completed role-based pre-verification system:**

1. **Database Structure:**
   - Updated `applications` table with new status enum and fields (score, meeting_notes)
   - Added demographics fields to `retailers` table for business information
   - Created `notifications` table for in-app/email notifications

2. **Service Layer Architecture:**
   - `VerificationService` - Handles role-based verification logic
   - `ApplicationService` - Manages vendor application workflow
   - `ApplicationRepository` - Data access layer for applications
   - All services properly bound in `RepositoryServiceProvider`

3. **Middleware & Routing:**
   - `EnsureRoleVerified` middleware enforces verification requirements
   - Verification routes for all roles (vendor, retailer, supplier, production-manager, hr-manager, general)
   - Protected routes require verification before access

4. **Pre-Verification Views:**
   - **Vendor:** PDF upload with scoring system, status tracking, tabbed interface with instructions
   - **Retailer:** Demographics form with business information, address, and market details
   - **Basic Roles:** Welcome pages with role-specific feature descriptions and admin approval flow
   - All views use consistent Tailwind CSS styling and responsive design

5. **Livewire Components:**
   - `VendorApplication` - PDF upload, status tracking, progress visualization
   - `RetailerDemographics` - Comprehensive demographics form with validation

6. **Application Workflow:**
   - Vendors: Upload PDF → Java server scoring → Admin meeting → Approval/Rejection
   - Retailers: Complete demographics → Immediate verification
   - Others: Admin approval required

7. **Admin Interface (Completed):**
   - **Application Management:** Complete CRUD operations for vendor applications
   - **Verification Dashboard:** Real-time stats, recent applications, status tracking
   - **Integrated Overview:** Main admin dashboard includes verification management
   - **Quick Actions:** Direct links to application management and user verification
   - **Responsive Design:** Mobile-friendly interface with consistent styling

8. **Notification System (Completed):**
   - **Email Notifications:** Laravel notification classes for all workflow stages
   - **Application Workflow Emails:** Received, scored, meeting scheduled, approved/rejected
   - **Admin Notifications:** New application submissions requiring review
   - **User Verification:** Welcome emails for completed verification
   - **In-App Notifications:** Real-time notification components with unread counts
   - **Notification Bell:** Dropdown navigation component with recent notifications
   - **Notification List:** Full notification management with mark as read functionality

**System supports complete role-based access control with pre-verification requirements, comprehensive admin management tools, and full notification workflow.**
